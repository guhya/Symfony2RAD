<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\EventVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use AppBundle\Validator\EventValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class EventController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/event/list", name="adminEventList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$eventVo	= new EventVO();
		$eventVo->setSearchCondition($a["c"]);
		$eventVo->setSearchKeyword($a["k"]);
		$eventVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$eventVo->setStartRow(($a["p"] - 1) * $eventVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.eventService")->countList($eventVo);		
		$result 	= $this->get("my.admin.eventService")->getList($eventVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $eventVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Event Management",
				Constants::CONTENT_MENU 		=> Constants::EVENT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::EVENT				=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $eventVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/event/eventList.html.twig", $content);
	}

	/**
	 * @Route("/admin/event/detail", name="adminEventDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$eventVo	= new EventVO();
		$eventVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.eventService")->getById($eventVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Event Management",
				Constants::CONTENT_MENU 		=> Constants::EVENT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::EVENT				=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/event/eventDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/event/write", name="adminEventWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$event	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$event		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($event, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.eventService")->insert($event, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/event/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Event Management",
				Constants::CONTENT_MENU 		=> Constants::EVENT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::EVENT				=> $event,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/event/eventForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/event/edit", name="adminEventEdit")
	 */
	public function editAction(Request $request)
	{	
		$eventSeq		= $request->get("seq");		
		
		$eventVo	= new EventVO();
		$eventVo->setSeq($eventSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$event	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$event		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($event, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $eventVo->getSeq());
				$r = $this->get("my.admin.eventService")->update($event, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/event/detail?seq=".$eventVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$event 	= $this->get("my.admin.eventService")->getById($eventVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Event Management",
				Constants::CONTENT_MENU 		=> Constants::EVENT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::EVENT				=> $event,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/event/eventForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/event/delete", name="adminEventDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$eventVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.eventService")->delete($eventVo);
	
		return $this->redirect(Constants::WEBROOT."admin/event/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$eventVo	= new EventVO();
		$eventVo->setSeq($request->request->get("seq"));
		$eventVo->setTitle($request->request->get("title"));
		$eventVo->setContent($request->request->get("content"));
		$eventVo->setStartDate($request->request->get("startDate"));
		$eventVo->setEndDate($request->request->get("endDate"));
		
		$eventVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$eventVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$eventVo->setRegId($this->get("session")->get("userSession")["username"]);
		$eventVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $eventVo;		
	}
	
	private function validate(EventVO $eventVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$eventValidator = new EventValidator($eventVo, $fileList);		
		$errors	= $validator->validate($eventValidator, null, array("Default"));	
		
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
