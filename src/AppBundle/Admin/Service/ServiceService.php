<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\ServiceDAO;
use AppBundle\VO\ServiceVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class ServiceService
{
	
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(ServiceDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(ServiceVO $serviceVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($serviceVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$serviceVo->getRegIp(), $serviceVo->getRegId(),
													$file, Constants::SERVICE, $key, Constants::UPLOAD_SERVICE_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_SERVICE_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($serviceVo->getRegIp(), $serviceVo->getRegId(), "", Constants::SERVICE, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($serviceVo->getRegIp(), $serviceVo->getRegId(), "", Constants::SERVICE, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(ServiceVO $serviceVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($serviceVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$serviceVo->getRegIp(), $serviceVo->getRegId(),
													$file, Constants::SERVICE, $key, Constants::UPLOAD_SERVICE_PATH
													);
			$fileVo->setOwnerSeq($serviceVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_SERVICE_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($serviceVo->getRegIp(), $serviceVo->getRegId(), "", Constants::SERVICE, "cat");
			$termRelationshipVo->setOwnerSeq($serviceVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($serviceVo->getRegIp(), $serviceVo->getRegId(), "", Constants::SERVICE, "tag");
			$termRelationshipVo->setOwnerSeq($serviceVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(ServiceVO $serviceVo)
	{
		return $this->dao->delete($serviceVo);
	}
	
	public function getById(ServiceVO $serviceVo)
	{
		$r = $this->dao->getById($serviceVo);
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($serviceVo->getRegIp(), $serviceVo->getRegId(), "", Constants::SERVICE, "tag");
			$termRelationshipVo->setOwnerSeq($serviceVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(ServiceVO $serviceVo)
	{
		return $this->dao->getList($serviceVo);
	}
	
	public function countList(ServiceVO $serviceVo)
	{
		return $this->dao->countList($serviceVo);
	}
	
	public function setViewCount(ServiceVO $serviceVo)
	{
		return $this->dao->setViewCount($serviceVo);
	}	
}
