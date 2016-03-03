<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\EventDAO;
use AppBundle\VO\EventVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class EventService
{
	
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(EventDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(EventVO $eventVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($eventVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$eventVo->getRegIp(), $eventVo->getRegId(),
													$file, Constants::EVENT, $key, Constants::UPLOAD_EVENT_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_EVENT_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($eventVo->getRegIp(), $eventVo->getRegId(), "", Constants::EVENT, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($eventVo->getRegIp(), $eventVo->getRegId(), "", Constants::EVENT, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(EventVO $eventVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($eventVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$eventVo->getRegIp(), $eventVo->getRegId(),
													$file, Constants::EVENT, $key, Constants::UPLOAD_EVENT_PATH
													);
			$fileVo->setOwnerSeq($eventVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_EVENT_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($eventVo->getRegIp(), $eventVo->getRegId(), "", Constants::EVENT, "cat");
			$termRelationshipVo->setOwnerSeq($eventVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($eventVo->getRegIp(), $eventVo->getRegId(), "", Constants::EVENT, "tag");
			$termRelationshipVo->setOwnerSeq($eventVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(EventVO $eventVo)
	{
		return $this->dao->delete($eventVo);
	}
	
	public function getById(EventVO $eventVo)
	{
		$r = $this->dao->getById($eventVo);
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($eventVo->getRegIp(), $eventVo->getRegId(), "", Constants::EVENT, "tag");
			$termRelationshipVo->setOwnerSeq($eventVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(EventVO $eventVo)
	{
		return $this->dao->getList($eventVo);
	}
	
	public function countList(EventVO $eventVo)
	{
		return $this->dao->countList($eventVo);
	}
	
	public function setViewCount(EventVO $eventVo)
	{
		return $this->dao->setViewCount($eventVo);
	}	
}
