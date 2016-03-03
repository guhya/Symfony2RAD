<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class FileVO extends CommonVO{
	
	private $category;
	private $channel;
	private $ownerSeq;
	private $name;
	private $originalName;
	private $size;
	private $path;
	
	public function setCategory($category){
		$this->category = $category;		
	}
	
	public function getCategory(){
		return $this->category;
	}

	
	public function setChannel($channel){
		$this->channel = $channel;
	}
	
	public function getChannel(){
		return $this->channel;
	}
	
	
	public function setOwnerSeq($ownerSeq){
		$this->ownerSeq = $ownerSeq;
	}
	
	public function getOwnerSeq(){
		return $this->ownerSeq;
	}
	
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	
	public function setOriginalName($originalName){
		$this->originalName = $originalName;
	}
	
	public function getOriginalName(){
		return $this->originalName;
	}
	
	
	public function setSize($size){
		$this->size = $size;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	
	public function setPath($path){
		$this->path = $path;
	}
	
	public function getPath(){
		return $this->path;
	}
	
		
}