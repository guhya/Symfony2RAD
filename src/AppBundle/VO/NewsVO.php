<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class NewsVO extends CommonVO{
	
	private $title;
	private $content;
	
	public function setTitle($title){
		$this->title = $title;
	}	
	public function getTitle(){
		return $this->title;
	}
		
	public function setContent($content){
		$this->content = $content;
	}	
	public function getContent(){
		return $this->content;
	}
	
}