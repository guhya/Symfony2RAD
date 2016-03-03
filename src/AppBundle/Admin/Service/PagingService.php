<?php
namespace AppBundle\Admin\Service;

class PagingService
{
	private $totalRows;
	private $pageSize;
	private $currentPage;
	private $totalPages;
	
	private $linkPage;
	private $linkParameter;
	
    private $strPrev 	= "<";
	private $strNext	= ">";                                                                
    private $strStart	= "<<";
	private $strEnd		= ">>";
	
	public function buildPage($totalRows, $pageSize, $currentPage, $linkPage = "", $linkParameter = "")
	{		
		$this->totalRows 		= $totalRows;
		$this->pageSize			= $pageSize;
		$this->currentPage		= $currentPage;
		
		$this->linkPage			= $linkPage;
		$this->linkParameter	= $linkParameter;
		
		if($totalRows > $pageSize){
			$this->totalPages	= ceil($totalRows / $pageSize);
		}else{
			$this->totalPages	= 0;
		}
		
		//Early exit if paging is non existent
		if ($this->totalPages == 0){
			return "";
		}
		
		$result  = "";
		$result	.= $this->firstPage();		
		$result	.= $this->prevPage();
		$result .= $this->listPage();
		$result	.= $this->nextPage();		
		$result	.= $this->lastPage();
		
		return $result;
	}
	
	private function firstPage(){
		$result = "";
		if($this->currentPage > 1){
			$result .= "<li><a href=".$this->linkPage."?p=1".$this->linkParameter." class='prev'>".$this->strStart."</a></li>"; 
		}else{
			$result .= "<li><a href='javascript:void(0);' class='prev'>".$this->strStart."</a></li>";				
		}
		
		return $result;
	}
	
	private function prevPage(){
		$result = "";
		if($this->currentPage > 1){
			$result .= "<li><a href=".$this->linkPage."?p=".($this->currentPage-1).$this->linkParameter." class='prev'>".$this->strPrev."</a></li>";
		}else{
			$result .= "<li><a href='javascript:void(0);' class='prev'>".$this->strPrev."</a></li>";
		}
		
		return $result;		
	}
	
	private function listPage(){
		$result = "";
		for($i=1; $i<=$this->totalPages; $i++){
			if($this->currentPage != $i){
				$result .= "<li><a href=".$this->linkPage."?p=".$i.$this->linkParameter.">".$i."</a></li>";				
			}else{
				$result .= "<li class='active'><a href='javascript:void(0);'>".$i."</a></li>";				
			}
		}
		
		return $result;
	}
	
	private function nextPage(){
		$result = "";
		if($this->currentPage < $this->totalPages){
			$result .= "<li><a href=".$this->linkPage."?p=".($this->currentPage+1).$this->linkParameter." class='next'>".$this->strNext."</a></li>";
		}else{
			$result .= "<li><a href='javascript:void(0);' class='next'>".$this->strNext."</a></li>";
		}
		
		return $result;		
	}
	
	private function lastPage(){
		$result = "";
		if($this->currentPage < $this->totalPages){
			$result .= "<li><a href=".$this->linkPage."?p=".$this->totalPages.$this->linkParameter." class='next'>".$this->strEnd."</a></li>";
		}else{
			$result .= "<li><a href='javascript:void(0);' class='next'>".$this->strEnd."</a></li>";
		}
		
		return $result;		
	}
	
	
}