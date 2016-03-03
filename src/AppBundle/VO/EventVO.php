<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class EventVO extends CommonVO{
	
	private $title;
	private $content;
	private $startDate;
	private $endDate;
	
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
	
	public function setStartDate($startDate){
		$this->startDate = $startDate;
	}
	public function getStartDate(){
		return $this->startDate;
	}
	
	public function setEndDate($endDate){
		$this->endDate = $endDate;
	}
	public function getEndDate(){
		return $this->endDate;
	}
	
}