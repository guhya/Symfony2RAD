<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\NewsVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use AppBundle\Validator\NewsValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class NewsController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/news/list", name="adminNewsList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$newsVo	= new NewsVO();
		$newsVo->setSearchCondition($a["c"]);
		$newsVo->setSearchKeyword($a["k"]);
		$newsVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$newsVo->setStartRow(($a["p"] - 1) * $newsVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.newsService")->countList($newsVo);		
		$result 	= $this->get("my.admin.newsService")->getList($newsVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $newsVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "News Management",
				Constants::CONTENT_MENU 		=> Constants::NEWS,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::NEWS					=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $newsVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/news/newsList.html.twig", $content);
	}

	/**
	 * @Route("/admin/news/detail", name="adminNewsDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$newsVo	= new NewsVO();
		$newsVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.newsService")->getById($newsVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "News Management",
				Constants::CONTENT_MENU 		=> Constants::NEWS,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::NEWS					=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/news/newsDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/news/write", name="adminNewsWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$news	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$news		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($news, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.newsService")->insert($news, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/news/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "News Management",
				Constants::CONTENT_MENU 		=> Constants::NEWS,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::NEWS					=> $news,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/news/newsForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/news/edit", name="adminNewsEdit")
	 */
	public function editAction(Request $request)
	{	
		$newsSeq		= $request->get("seq");		
		
		$newsVo	= new NewsVO();
		$newsVo->setSeq($newsSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$news	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$news		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($news, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $newsVo->getSeq());
				$r = $this->get("my.admin.newsService")->update($news, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/news/detail?seq=".$newsVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$news 	= $this->get("my.admin.newsService")->getById($newsVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "News Management",
				Constants::CONTENT_MENU 		=> Constants::NEWS,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::NEWS					=> $news,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/news/newsForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/news/delete", name="adminNewsDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$newsVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.newsService")->delete($newsVo);
	
		return $this->redirect(Constants::WEBROOT."admin/news/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$newsVo	= new NewsVO();
		$newsVo->setSeq($request->request->get("seq"));
		$newsVo->setTitle($request->request->get("title"));
		$newsVo->setContent($request->request->get("content"));
		
		$newsVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$newsVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$newsVo->setRegId($this->get("session")->get("userSession")["username"]);
		$newsVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $newsVo;		
	}
	
	private function validate(NewsVO $newsVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$newsValidator = new NewsValidator($newsVo, $fileList);		
		$errors	= $validator->validate($newsValidator, null, array("Default"));	
		
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
