<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\ServiceVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\ServiceValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class ServiceController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/service/list", name="adminServiceList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$serviceVo	= new ServiceVO();
		$serviceVo->setSearchCondition($a["c"]);
		$serviceVo->setSearchKeyword($a["k"]);
		$serviceVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$serviceVo->setStartRow(($a["p"] - 1) * $serviceVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.serviceService")->countList($serviceVo);		
		$result 	= $this->get("my.admin.serviceService")->getList($serviceVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $serviceVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Service Management",
				Constants::CONTENT_MENU 		=> Constants::SERVICE,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::SERVICE				=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $serviceVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/service/serviceList.html.twig", $content);
	}

	/**
	 * @Route("/admin/service/detail", name="adminServiceDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$serviceVo	= new ServiceVO();
		$serviceVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.serviceService")->getById($serviceVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Service Management",
				Constants::CONTENT_MENU 		=> Constants::SERVICE,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::SERVICE				=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/service/serviceDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/service/write", name="adminServiceWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$service	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$service		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($service, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.serviceService")->insert($service, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/service/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Service Management",
				Constants::CONTENT_MENU 		=> Constants::SERVICE,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::SERVICE				=> $service,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/service/serviceForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/service/edit", name="adminServiceEdit")
	 */
	public function editAction(Request $request)
	{	
		$serviceSeq		= $request->get("seq");		
		
		$serviceVo	= new ServiceVO();
		$serviceVo->setSeq($serviceSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$service	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$service		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($service, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $serviceVo->getSeq());
				$r = $this->get("my.admin.serviceService")->update($service, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/service/detail?seq=".$serviceVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$service 	= $this->get("my.admin.serviceService")->getById($serviceVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Service Management",
				Constants::CONTENT_MENU 		=> Constants::SERVICE,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::SERVICE				=> $service,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/service/serviceForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/service/delete", name="adminServiceDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$serviceVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.serviceService")->delete($serviceVo);
	
		return $this->redirect(Constants::WEBROOT."admin/service/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
	}
	
	private function buildPaginationUrl(Request $request)
	{
		$p = $request->get(Constants::PARAMETER_PAGE) ? $request->get(Constants::PARAMETER_PAGE) : 1;
		$c = $request->get(Constants::PARAMETER_CONDITION);
		$k = $request->get(Constants::PARAMETER_KEYWORD);
		
		$parameter 	= "&".Constants::PARAMETER_CONDITION."=".$c."&".Constants::PARAMETER_KEYWORD."=".$k;
		
		$a = array("p" => $p, "c" => $c, "k" => $k, "param" => $parameter); 
		
		return $a;
	}
	
	private function prepareVo(Request $request)
	{
		$serviceVo	= new ServiceVO();
		$serviceVo->setSeq($request->request->get("seq"));
		$serviceVo->setName($request->request->get("name"));
		$serviceVo->setDescription($request->request->get("description"));
		
		$serviceVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$serviceVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$serviceVo->setRegId($this->get("session")->get("userSession")["username"]);
		$serviceVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $serviceVo;		
	}
	
	private function validate(ServiceVO $serviceVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$serviceValidator = new ServiceValidator($serviceVo, $fileList);		
		$errors	= $validator->validate($serviceValidator, null, array("Default"));	
		
		return $errors;
	}
	
	private function deleteFile($deletedFiles, $ownerSeq)
	{
		/* Delete file if told so */
		if($deletedFiles != ""){
			$arrDeletedFiles = split(",", $deletedFiles);
			foreach ($arrDeletedFiles as $deletedSeq){		
				$fileVo 	= VOHelper::prepareFileVo($_SERVER["REMOTE_ADDR"], $this->get("session")->get("userSession")["username"]);
				$fileVo->setSeq($deletedSeq);
				$fileVo->setOwnerSeq($ownerSeq);
				
				$this->get("my.common.fileService")->delete($fileVo);
			}
		}
	}

	
}
