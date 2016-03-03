<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class CatalogVO extends CommonVO{
	
	private $name;
	private $description;
	private $url;
	
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
	
	public function setUrl($url){
		$this->url = $url;
	}
	public function getUrl(){
		return $this->url;
	}

}