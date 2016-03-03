<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\TermDAO;
use AppBundle\VO\TermVO;
use AppBundle\Common\Constants;

class TermService
{
	private $dao;
	
	public function __construct(TermDAO $dao)
	{
		$this->dao 		= $dao;
	}
	
	public function insert(TermVO $termVo)
	{
		/* 
		 * If category insert, treat differently
		 * Because we need to store lineage/path of category
		 */ 
		if($termVo->getTaxonomy() == "cat"){
			if($termVo->getParent() == "0"){
				/*
				 * For parent categories, lineage is the sequence itself 
				 * padded 4 times with 0 and ended by a slash 
				 */
				$r = $this->dao->insertParentCategory($termVo);
			}else{
				/*
				 * For child categories, lineage is the lineage of parent
				 * plus the sequence of this category padded 4 times with 0 ended by a slash
				 */
				$r = $this->dao->insertChildCategory($termVo);
			}
		}else{
			$r = $this->dao->insert($termVo);
		}
		
		return $r;
	}
	
	public function update(TermVO $termVo)
	{
		$r = $this->dao->update($termVo);
		return $r;		
	}
	
	public function delete(TermVO $termVo)
	{
		return $this->dao->delete($termVo);
	}
	
	public function getById(TermVO $termVo)
	{
		$r = $this->dao->getById($termVo);
		foreach ($r as $k => $v){
			if($r["lineage"] != ""){
				$r1 = $this->dao->getFullPath($termVo->getSeq());
				$r["fullPath"] = $r1;
			}
		}
		
		return $r;
	}

	public function getList(TermVO $termVo)
	{
		$r = $this->dao->getList($termVo);
		
		//Get full path of the categories
		foreach ($r as $k => $v){
			if($v["lineage"] != ""){
				$r1 = $this->dao->getFullPath($v["seq"]);
				$r[$k]["fullPath"] = $r1;
			}
		}
		
		return $r;
	}
	
	public function countList(TermVO $termVo)
	{
		return $this->dao->countList($termVo);
	}
	
	public function setViewCount(TermVO $termVo)
	{
		return $this->dao->setViewCount($termVo);
	}
	
	public function getTags()
	{
		return $this->dao->getTags();
	}
	
	public function getCategories()
	{
		return $this->dao->getCategories();
	}
	
	public function getCategoriesByParent(TermVO $termVo)
	{
		return $this->dao->getCategoriesByParent($termVo);
	}
		
}
