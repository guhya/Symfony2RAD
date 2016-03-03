<?php
namespace AppBundle\Admin\Listener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Common\Constants;

class AuthenticationListenerImpl
{
	
	public function onKernelController(FilterControllerEvent $event)
	{
		$controller = $event->getController();
		$request 	= $event->getRequest();
		$route		= $request->attributes->get("_route");
		
		if (!is_array($controller)) {
			return;
		}
		
		if($controller[0] instanceof IAuthenticationListener){
			#$userSession = array("username" => "admin");
			#$request->getSession()->set("userSession", $userSession);
			
			//var_dump($_SESSION);exit();
			#return;
			
			$userSession	= $request->getSession()->get("userSession");			
			if(!$userSession){
				header("Location: ".Constants::WEBROOT."admin/login");
				exit();
			}
		}		
	}
	
}