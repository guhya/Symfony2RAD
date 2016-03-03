<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\UserVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\UserValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class UserController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/user/list", name="adminUserList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$userVo	= new UserVO();
		$userVo->setSearchCondition($a["c"]);
		$userVo->setSearchKeyword($a["k"]);
		$userVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$userVo->setStartRow(($a["p"] - 1) * $userVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.userService")->countList($userVo);		
		$result 	= $this->get("my.admin.userService")->getList($userVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $userVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "User Management",
				Constants::CONTENT_MENU 		=> Constants::USER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::USER					=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $userVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/user/userList.html.twig", $content);
	}

	/**
	 * @Route("/admin/user/detail", name="adminUserDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$userVo	= new UserVO();
		$userVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.userService")->getById($userVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "User Management",
				Constants::CONTENT_MENU 		=> Constants::USER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::USER					=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/user/userDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/user/write", name="adminUserWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$user	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$user			= $this->prepareVo($request);
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($user, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.userService")->insert($user, $fileList);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/user/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "User Management",
				Constants::CONTENT_MENU 		=> Constants::USER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::ACTION				=> Constants::WRITE,
				Constants::USER					=> $user,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/user/userForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/user/edit", name="adminUserEdit")
	 */
	public function editAction(Request $request)
	{	
		$userSeq		= $request->get("seq");		
		
		$userVo	= new UserVO();
		$userVo->setSeq($userSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$user		= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$user			= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$fileList		= $request->files->all();
			
			$errors			= $this->validate($user, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $userVo->getSeq());
				$r = $this->get("my.admin.userService")->update($user, $fileList);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/user/detail?seq=".$userVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$user 	= $this->get("my.admin.userService")->getById($userVo);
		}		
	
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "User Management",
				Constants::CONTENT_MENU 		=> Constants::USER,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::ACTION				=> Constants::EDIT,
				Constants::USER					=> $user,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/user/userForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/user/delete", name="adminUserDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$userVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.userService")->delete($userVo);
	
		return $this->redirect(Constants::WEBROOT."admin/user/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
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
		$userVo	= new UserVO();
		$userVo->setSeq($request->request->get("seq"));
		$userVo->setUsername($request->request->get("username"));
		
		$userVo->setPassword(hash("sha256", $request->request->get("password")));
		$userVo->setOldPassword(hash("sha256", $request->request->get("oldPassword")));
		
		$userVo->setFirstName($request->request->get("firstName"));
		$userVo->setLastName($request->request->get("lastName"));
		$userVo->setEmail($request->request->get("email"));
		
		$userVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$userVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$userVo->setRegId($this->get("session")->get("userSession")["username"]);
		$userVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $userVo;		
	}
	
	private function validate(UserVO $userVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$userValidator = new UserValidator($userVo, $fileList, $this->get("my.admin.userService"));		
		$errors	= $validator->validate($userValidator, null, array("Default"));
		
		//var_dump(hash("sha256", "ewide1234"));exit();
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
