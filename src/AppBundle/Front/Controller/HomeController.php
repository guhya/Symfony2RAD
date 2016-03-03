<?php

namespace AppBundle\Front\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Common\Constants;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
	
	/**
	 * @Route("/", name="home")
	 */	
	public function home(Request $request) {
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Casper"
		);
		
		return $this->render("front/home/home.html.twig", $content);
	}
	
}
