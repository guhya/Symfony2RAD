<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\UserVO;
use AppBundle\VO\CompanyVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\UserValidator;

class LoginController extends Controller
{
	/**
	 * @Route("/admin/login", name="adminLogin")
	 */
	public function loginAction(Request $request)
	{
		if($this->get("session")->get("userSession")) {
			return $this->redirect(Constants::WEBROOT."admin/product/list", 301);
			exit();
		}
		
		/* Check if it is from submitted form */
		$result 	= "";
		$content 	= array();
		if($request->isMethod("POST")){
			
			$userVo	= new UserVO();
			$userVo->setUsername($request->request->get("username"));
			$password = hash("sha256", $request->request->get("password"));
			$userVo->setPassword($password);
			
			$result = $this->get("my.admin.userService")->getByUsername($userVo);
			
			/* If login success, then redirect to dashboard page */
			if($result){
				$session = $this->getRequest()->getSession();
				$session->set("userSession", $result);
				
				return $this->redirect(Constants::WEBROOT."admin/product/list", 301);
			}else{
				$content = array(
						"error"	=> "Username or password does not exist!"
				);
				return $this->render("admin/login/login.html.twig", $content);				
			}
		}else{
			return $this->render("admin/login/login.html.twig", $content);				
		}
	}
	
	/**
	 * @Route("/admin/logout", name="adminLogout")
	 */
	public function logoutAction(Request $request)
	{
		$this->get("session")->clear();
		$this->get("session")->invalidate();
		return $this->redirect(Constants::WEBROOT."admin/login", 301);
	}	
}
