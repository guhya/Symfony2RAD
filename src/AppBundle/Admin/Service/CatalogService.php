<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\CatalogDAO;
use AppBundle\VO\CatalogVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class CatalogService
{
	
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(CatalogDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(CatalogVO $catalogVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($catalogVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$catalogVo->getRegIp(), $catalogVo->getRegId(),
													$file, Constants::CATALOG, $key, Constants::UPLOAD_CATALOG_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_CATALOG_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($catalogVo->getRegIp(), $catalogVo->getRegId(), "", Constants::CATALOG, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($catalogVo->getRegIp(), $catalogVo->getRegId(), "", Constants::CATALOG, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(CatalogVO $catalogVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($catalogVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$catalogVo->getRegIp(), $catalogVo->getRegId(),
													$file, Constants::CATALOG, $key, Constants::UPLOAD_CATALOG_PATH
													);
			$fileVo->setOwnerSeq($catalogVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_CATALOG_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($catalogVo->getRegIp(), $catalogVo->getRegId(), "", Constants::CATALOG, "cat");
			$termRelationshipVo->setOwnerSeq($catalogVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($catalogVo->getRegIp(), $catalogVo->getRegId(), "", Constants::CATALOG, "tag");
			$termRelationshipVo->setOwnerSeq($catalogVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(CatalogVO $catalogVo)
	{
		return $this->dao->delete($catalogVo);
	}
	
	public function getById(CatalogVO $catalogVo)
	{
		$r = $this->dao->getById($catalogVo);
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($catalogVo->getRegIp(), $catalogVo->getRegId(), "", Constants::CATALOG, "tag");
			$termRelationshipVo->setOwnerSeq($catalogVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(CatalogVO $catalogVo)
	{
		return $this->dao->getList($catalogVo);
	}
	
	public function countList(CatalogVO $catalogVo)
	{
		return $this->dao->countList($catalogVo);
	}
	
	public function setViewCount(CatalogVO $catalogVo)
	{
		return $this->dao->setViewCount($catalogVo);
	}	
}
