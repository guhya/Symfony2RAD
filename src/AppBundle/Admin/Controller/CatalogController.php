<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\CatalogVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\CatalogValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class CatalogController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/catalog/list", name="adminCatalogList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$catalogVo	= new CatalogVO();
		$catalogVo->setSearchCondition($a["c"]);
		$catalogVo->setSearchKeyword($a["k"]);
		$catalogVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$catalogVo->setStartRow(($a["p"] - 1) * $catalogVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.catalogService")->countList($catalogVo);		
		$result 	= $this->get("my.admin.catalogService")->getList($catalogVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $catalogVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Catalog Management",
				Constants::CONTENT_MENU 		=> Constants::CATALOG,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CATALOG				=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $catalogVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/catalog/catalogList.html.twig", $content);
	}

	/**
	 * @Route("/admin/catalog/detail", name="adminCatalogDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$catalogVo	= new CatalogVO();
		$catalogVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.catalogService")->getById($catalogVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Catalog Management",
				Constants::CONTENT_MENU 		=> Constants::CATALOG,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CATALOG				=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/catalog/catalogDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/catalog/write", name="adminCatalogWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$catalog	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$catalog		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($catalog, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.catalogService")->insert($catalog, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/catalog/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Catalog Management",
				Constants::CONTENT_MENU 		=> Constants::CATALOG,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CATALOG				=> $catalog,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/catalog/catalogForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/catalog/edit", name="adminCatalogEdit")
	 */
	public function editAction(Request $request)
	{	
		$catalogSeq		= $request->get("seq");		
		
		$catalogVo	= new CatalogVO();
		$catalogVo->setSeq($catalogSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$catalog	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$catalog		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($catalog, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $catalogVo->getSeq());
				$r = $this->get("my.admin.catalogService")->update($catalog, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/catalog/detail?seq=".$catalogVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$catalog 	= $this->get("my.admin.catalogService")->getById($catalogVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Catalog Management",
				Constants::CONTENT_MENU 		=> Constants::CATALOG,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::CATALOG				=> $catalog,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/catalog/catalogForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/catalog/delete", name="adminCatalogDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$catalogVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.catalogService")->delete($catalogVo);
	
		return $this->redirect(Constants::WEBROOT."admin/catalog/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$catalogVo	= new CatalogVO();
		$catalogVo->setSeq($request->request->get("seq"));
		$catalogVo->setName($request->request->get("name"));
		$catalogVo->setDescription($request->request->get("description"));
		$catalogVo->setUrl($request->request->get("url"));
		
		$catalogVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$catalogVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$catalogVo->setRegId($this->get("session")->get("userSession")["username"]);
		$catalogVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $catalogVo;		
	}
	
	private function validate(CatalogVO $catalogVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$catalogValidator = new CatalogValidator($catalogVo, $fileList);		
		$errors	= $validator->validate($catalogValidator, null, array("Default"));	
		
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
