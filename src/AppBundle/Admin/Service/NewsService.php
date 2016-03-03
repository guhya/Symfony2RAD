<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\NewsDAO;
use AppBundle\VO\NewsVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class NewsService
{
	
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(NewsDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(NewsVO $newsVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($newsVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$newsVo->getRegIp(), $newsVo->getRegId(),
													$file, Constants::NEWS, $key, Constants::UPLOAD_NEWS_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_NEWS_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($newsVo->getRegIp(), $newsVo->getRegId(), "", Constants::NEWS, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($newsVo->getRegIp(), $newsVo->getRegId(), "", Constants::NEWS, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(NewsVO $newsVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($newsVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$newsVo->getRegIp(), $newsVo->getRegId(),
													$file, Constants::NEWS, $key, Constants::UPLOAD_NEWS_PATH
													);
			$fileVo->setOwnerSeq($newsVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_NEWS_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($newsVo->getRegIp(), $newsVo->getRegId(), "", Constants::NEWS, "cat");
			$termRelationshipVo->setOwnerSeq($newsVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($newsVo->getRegIp(), $newsVo->getRegId(), "", Constants::NEWS, "tag");
			$termRelationshipVo->setOwnerSeq($newsVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(NewsVO $newsVo)
	{
		return $this->dao->delete($newsVo);
	}
	
	public function getById(NewsVO $newsVo)
	{
		$r = $this->dao->getById($newsVo);
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($newsVo->getRegIp(), $newsVo->getRegId(), "", Constants::NEWS, "tag");
			$termRelationshipVo->setOwnerSeq($newsVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(NewsVO $newsVo)
	{
		return $this->dao->getList($newsVo);
	}
	
	public function countList(NewsVO $newsVo)
	{
		return $this->dao->countList($newsVo);
	}
	
	public function setViewCount(NewsVO $newsVo)
	{
		return $this->dao->setViewCount($newsVo);
	}	
}
