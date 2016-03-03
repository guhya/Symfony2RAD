<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class TermVO extends CommonVO{
	
	private $name;
	private $description;
	private $taxonomy;
	private $lineage;
	private $parent;
	
	public function setName($name){
		$this->name = $name;
	}	
	public function getName(){
		return $this->name;
	}
		
	public function setDescription($description){
		$this->description = $description;
	}	
	public function getDescription(){
		return $this->description;
	}
	
	public function setTaxonomy($taxonomy){
		$this->taxonomy = $taxonomy;
	}
	public function getTaxonomy(){
		return $this->taxonomy;
	}

	public function setLineage($lineage){
		$this->lineage = $lineage;
	}
	public function getLineage(){
		return $this->lineage;
	}

	public function setParent($parent){
		$this->parent = $parent;
	}
	public function getParent(){
		return $this->parent;
	}

	
}