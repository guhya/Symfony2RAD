<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\CareerVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use AppBundle\Validator\CareerValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class CareerController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/career/list", name="adminCareerList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$careerVo	= new CareerVO();
		$careerVo->setSearchCondition($a["c"]);
		$careerVo->setSearchKeyword($a["k"]);
		$careerVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$careerVo->setStartRow(($a["p"] - 1) * $careerVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.careerService")->countList($careerVo);		
		$result 	= $this->get("my.admin.careerService")->getList($careerVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $careerVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Career Management",
				Constants::CONTENT_MENU 		=> Constants::CAREER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CAREER				=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $careerVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/career/careerList.html.twig", $content);
	}

	/**
	 * @Route("/admin/career/detail", name="adminCareerDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$careerVo	= new CareerVO();
		$careerVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.careerService")->getById($careerVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Career Management",
				Constants::CONTENT_MENU 		=> Constants::CAREER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CAREER				=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/career/careerDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/career/write", name="adminCareerWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$career	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$career		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($career, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.careerService")->insert($career, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/career/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Career Management",
				Constants::CONTENT_MENU 		=> Constants::CAREER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CAREER				=> $career,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/career/careerForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/career/edit", name="adminCareerEdit")
	 */
	public function editAction(Request $request)
	{	
		$careerSeq		= $request->get("seq");		
		
		$careerVo	= new CareerVO();
		$careerVo->setSeq($careerSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$career	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$career		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($career, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $careerVo->getSeq());
				$r = $this->get("my.admin.careerService")->update($career, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/career/detail?seq=".$careerVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$career 	= $this->get("my.admin.careerService")->getById($careerVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Career Management",
				Constants::CONTENT_MENU 		=> Constants::CAREER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CAREER				=> $career,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/career/careerForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/career/delete", name="adminCareerDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$careerVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.careerService")->delete($careerVo);
	
		return $this->redirect(Constants::WEBROOT."admin/career/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$careerVo	= new CareerVO();
		$careerVo->setSeq($request->request->get("seq"));
		$careerVo->setTitle($request->request->get("title"));
		$careerVo->setContent($request->request->get("content"));
		$careerVo->setStartDate($request->request->get("startDate"));
		$careerVo->setEndDate($request->request->get("endDate"));
		
		$careerVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$careerVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$careerVo->setRegId($this->get("session")->get("userSession")["username"]);
		$careerVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $careerVo;		
	}
	
	private function validate(CareerVO $careerVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$careerValidator = new CareerValidator($careerVo, $fileList);		
		$errors	= $validator->validate($careerValidator, null, array("Default"));	
		
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
