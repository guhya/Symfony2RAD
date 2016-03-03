<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Common\Constants;
use AppBundle\VO\FileVO;

class CommonController extends Controller
{
	
	/**
	 * @Route("/image/{id}", name="getImage")
	 */	
	public function imageAction($id) {
		$fileVo 		= new FileVO();
		$fileVo->setSeq($id);
		
		$image 			= $this->get("my.common.fileService")->getById($fileVo);
		$path 			= Constants::WEBROOT_PHYSICAL.$image["path"]."/".$image["name"];
		
		if(file_exists($path)){			
			$file		= new File($path);
			$mimeType	= $file->getMimeType();
				
			$image 		= file_get_contents($file);
			$response 	= new Response($image);
			
			$response->headers->set("Content-Type", $mimeType);
			return $response;
		}
	}
	
	/**
	 * @Route("/download/{id}", name="getFile")
	 */	
	public function downloadAction($id) {
		$fileVo 		= new FileVO();
		$fileVo->setSeq($id);
		
		$file 			= $this->get("my.common.fileService")->getById($fileVo);
		$path 			= Constants::WEBROOT_PHYSICAL.$file["path"]."/".$file["name"];
		
		if(file_exists($path)){			
			$pFile		= new File($path);
			$mimeType	= $pFile->getMimeType();
				
			$download 	= file_get_contents($pFile);
			$response 	= new Response($download);
			
			$response->headers->set("Content-Type", $mimeType);
			$response->headers->set("Content-Length", filesize($pFile));
			$response->headers->set("Content-Disposition", "attachment; filename=\"". $file["originalName"] ."\";");
			return $response;
		}
	}
	
}
