<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Common\Constants;
use AppBundle\VO\FileVO;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Admin\Listener\IAuthenticationListener;

class DashboardController extends Controller implements IAuthenticationListener
{
	
	/**
	 * @Route("/admin/dashboard", name="dashboard")
	 */	
	public function dashboard(Request $request) {
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Dashboard",
				Constants::CONTENT_MENU 		=> Constants::DASHBOARD
		);
		
		return $this->render("admin/dashboard/dashboard.html.twig", $content);
	}
	
}
