<?php 
namespace AppBundle\Common;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\VO\FileVO;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\VO\TermRelationshipVO;

class VOHelper
{
	public function __construct(){
		
	}
	
	public static function prepareFileVo($ip, $username, File $file = null, $channel = null, $category = null, $path = null)
	{
		$fileVo = new FileVO();
		$fileVo->setRegIp($ip);
		$fileVo->setModIp($ip);
		$fileVo->setRegId($username);
		$fileVo->setModId($username);
		
		if ($file != null){
			$fileVo->setCategory($category);
			$fileVo->setChannel($channel);
			
			$fileVo->setName($channel."_".$category."_".round(microtime(true) * 1000).".".$file->getClientOriginalExtension());
			$fileVo->setOriginalName($file->getClientOriginalName());
			$fileVo->setSize($file->getClientSize());
			$fileVo->setPath($path);
		}
		
		return $fileVo;		
	}
	
	public static function prepareTermRelationshipVo($ip, $username, $termSeq, $channel, $taxonomy)
	{
		$termRelationshipVo = new TermRelationshipVO();
		$termRelationshipVo->setChannel($channel);
		$termRelationshipVo->setTermSeq($termSeq);
		$termRelationshipVo->setTaxonomy($taxonomy);
		
		$termRelationshipVo->setRegIp($ip);
		$termRelationshipVo->setModIp($ip);
		$termRelationshipVo->setRegId($username);
		$termRelationshipVo->setModId($username);
		
		return $termRelationshipVo;
	}	
}
?>