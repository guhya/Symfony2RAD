<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\TermVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\TermValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;

class TermController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/term/list", name="adminTermList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$termVo	= new TermVO();
		$termVo->setSearchCondition($a["c"]);
		$termVo->setSearchKeyword($a["k"]);
		$termVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$termVo->setStartRow(($a["p"] - 1) * $termVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.termService")->countList($termVo);		
		$result 	= $this->get("my.admin.termService")->getList($termVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $termVo->getPageSize(), $a["p"], "", $a["param"]);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Term Management",
				Constants::CONTENT_MENU 		=> Constants::TERM,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::TERM					=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $termVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/term/termList.html.twig", $content);
	}

	/**
	 * @Route("/admin/term/detail", name="adminTermDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$termVo	= new TermVO();
		$termVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.termService")->getById($termVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Term Management",
				Constants::CONTENT_MENU 		=> Constants::TERM,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::TERM					=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/term/termDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/term/write", name="adminTermWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$term		= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$term		= $this->prepareVo($request);
			$isTag		= $request->get("taxonomy") == "tag" ? true : false;
			$errors		= $this->validate($term, $isTag);
				
			if(count($errors) == 0) {
				$r = $this->get("my.admin.termService")->insert($term);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/term/list", 301);
				}
			}
		}else{
			$a		= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Term Management",
				Constants::CONTENT_MENU 		=> Constants::TERM,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::ACTION				=> Constants::WRITE,
				Constants::TERM					=> $term,
				Constants::CATEGORY				=> $cat,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/term/termForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/term/edit", name="adminTermEdit")
	 */
	public function editAction(Request $request)
	{	
		$termSeq		= $request->get("seq");		
		
		$termVo	= new TermVO();
		$termVo->setSeq($termSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$term		= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$term		= $this->prepareVo($request);
			$isTag		= $request->get("taxonomy") == "tag" ? true : false; 
			$errors		= $this->validate($term, $isTag);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.termService")->update($term);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/term/detail?seq=".$termVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$term 	= $this->get("my.admin.termService")->getById($termVo);
		}		
	
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Term Management",
				Constants::CONTENT_MENU 		=> Constants::TERM,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::ACTION				=> Constants::EDIT,
				Constants::TERM					=> $term,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/term/termForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/term/delete", name="adminTermDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$termVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.termService")->delete($termVo);
	
		return $this->redirect(Constants::WEBROOT."admin/term/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$termVo	= new TermVO();
		$termVo->setSeq($request->request->get("seq"));
		$termVo->setName($request->request->get("name"));
		$termVo->setDescription($request->request->get("description"));
		$termVo->setTaxonomy($request->request->get("taxonomy"));
		$termVo->setParent($request->request->get("parent"));
		$termVo->setLineage(substr($request->request->get("lineage"), 0, -1));
		
		$termVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$termVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$termVo->setRegId($this->get("session")->get("userSession")["username"]);
		$termVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $termVo;		
	}
	
	private function validate(TermVO $termVo, $isTag = false)
	{
		$validator 	= $this->get("validator");
		$termValidator = new TermValidator($termVo);		
		if($isTag){
			$errors	= $validator->validate($termValidator, null, array("Default"));
		}else{
			$errors	= $validator->validate($termValidator, null, array("Default", "Category"));				
		}
		
		return $errors;
	}
	
}
