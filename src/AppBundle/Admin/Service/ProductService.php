<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\ProductDAO;
use AppBundle\VO\ProductVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;
use Doctrine\DBAL\Connection;

class ProductService
{
	private $con;
	private $dao;
	private $fileDao;
	private $termRelationshipDao;
	
	public function __construct(Connection $con, ProductDAO $dao, FileDAO $fileDao, TermRelationshipDAO $termRelationshipDao)
	{
		$this->con					= $con;
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
		$this->termRelationshipDao	= $termRelationshipDao;
	}
	
	public function insert(ProductVO $productVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->insert($productVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$productVo->getRegIp(), $productVo->getRegId(),
													$file, Constants::PRODUCT, $key, Constants::UPLOAD_PRODUCT_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_PRODUCT_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($productVo->getRegIp(), $productVo->getRegId(), "", Constants::PRODUCT, "cat");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($productVo->getRegIp(), $productVo->getRegId(), "", Constants::PRODUCT, "tag");
			$termRelationshipVo->setOwnerSeq($r);
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
	}
	
	public function update(ProductVO $productVo, $fileList, $categories, $tags)
	{
		$r = $this->dao->update($productVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$productVo->getRegIp(), $productVo->getRegId(),
													$file, Constants::PRODUCT, $key, Constants::UPLOAD_PRODUCT_PATH
													);
			$fileVo->setOwnerSeq($productVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_PRODUCT_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		if($categories != ""){
			$arrCategories = split(",", $categories);
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($productVo->getRegIp(), $productVo->getRegId(), "", Constants::PRODUCT, "cat");
			$termRelationshipVo->setOwnerSeq($productVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrCategories as $cat){
				$termRelationshipVo->setTermSeq($cat);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
 		if(is_array($tags)){
			$arrTags = $tags;
			
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($productVo->getRegIp(), $productVo->getRegId(), "", Constants::PRODUCT, "tag");
			$termRelationshipVo->setOwnerSeq($productVo->getSeq());
			$tdr = $this->termRelationshipDao->deleteByOwner($termRelationshipVo);
			
			foreach($arrTags as $tag){
				$termRelationshipVo->setTermSeq($tag);
				$tsr = $this->termRelationshipDao->insert($termRelationshipVo);
			}
		}
		
		return $r;
		
	}
	
	public function delete(ProductVO $productVo)
	{
		return $this->dao->delete($productVo);
	}
	
	public function getById(ProductVO $productVo)
	{
		$r = $this->dao->getById($productVo);		
		
		foreach ($r as $k => $v){
			$termRelationshipVo = VOHelper::prepareTermRelationshipVo($productVo->getRegIp(), $productVo->getRegId(), "", Constants::PRODUCT, "tag");
			$termRelationshipVo->setOwnerSeq($productVo->getSeq());
			$tags = $this->termRelationshipDao->getByOwner($termRelationshipVo);
			$r["tags"] = $tags;
		}
		
		return $r;
	}

	public function getList(ProductVO $productVo)
	{
		return $this->dao->getList($productVo);
	}
	
	public function countList(ProductVO $productVo)
	{
		return $this->dao->countList($productVo);
	}
	
	public function setViewCount(ProductVO $productVo)
	{
		return $this->dao->setViewCount($productVo);
	}	
}
