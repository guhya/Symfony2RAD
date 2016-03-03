<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class TermRelationshipVO extends CommonVO{
	
	private $channel;
	private $ownerSeq;
	private $termSeq;
	private $taxonomy;
	
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
	
	public function setTermSeq($termSeq){
		$this->termSeq = $termSeq;
	}
	
	public function getTermSeq(){
		return $this->termSeq;
	}
		
	public function setTaxonomy($taxonomy){
		$this->taxonomy = $taxonomy;
	}
	
	public function getTaxonomy(){
		return $this->taxonomy;
	}

}