<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\CareerDAO;
use AppBundle\VO\CareerVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class CareerService
{
	
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(CareerDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(CareerVO $careerVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($careerVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$careerVo->getRegIp(), $careerVo->getRegId(),
													$file, Constants::CAREER, $key, Constants::UPLOAD_CAREER_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_CAREER_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($careerVo->getRegIp(), $careerVo->getRegId(), "", Constants::CAREER, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($careerVo->getRegIp(), $careerVo->getRegId(), "", Constants::CAREER, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(CareerVO $careerVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($careerVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$careerVo->getRegIp(), $careerVo->getRegId(),
													$file, Constants::CAREER, $key, Constants::UPLOAD_CAREER_PATH
													);
			$fileVo->setOwnerSeq($careerVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_CAREER_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($careerVo->getRegIp(), $careerVo->getRegId(), "", Constants::CAREER, "cat");
			$termRelationshipVo->setOwnerSeq($careerVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($careerVo->getRegIp(), $careerVo->getRegId(), "", Constants::CAREER, "tag");
			$termRelationshipVo->setOwnerSeq($careerVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(CareerVO $careerVo)
	{
		return $this->dao->delete($careerVo);
	}
	
	public function getById(CareerVO $careerVo)
	{
		$r = $this->dao->getById($careerVo);
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($careerVo->getRegIp(), $careerVo->getRegId(), "", Constants::CAREER, "tag");
			$termRelationshipVo->setOwnerSeq($careerVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(CareerVO $careerVo)
	{
		return $this->dao->getList($careerVo);
	}
	
	public function countList(CareerVO $careerVo)
	{
		return $this->dao->countList($careerVo);
	}
	
	public function setViewCount(CareerVO $careerVo)
	{
		return $this->dao->setViewCount($careerVo);
	}	
}
