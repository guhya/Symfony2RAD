<?php
namespace AppBundle\VO;

/* 
 * This is common value object that should be subclassed before using it. 
 * Act as a placeholder for the passed data. Between view, controller and service.
 * Why we include search related fields ? Because it will be sent to the service layer
 * Why we include errors related fields ? Because it will be populated after validation
 * 										  and view layer should get the result from this placeholder
 * Why we include pagination related fields ? Because we will need to display paged result  
*/

abstract class CommonVO
{
	private $seq;
	private $regIp;
	private $modIp;
	private $regId;
	private $regDate;
	private $modId;
	private $modDate;
	private $delYn;
	
	/* Search related field */
	private $searchCondition;
	private $searchKeyword;
	private $startRow;
	private $endRow;
	private $pageSize;
	
	/* Validation */
	private $errors;
	
	
	/* Sequence */
	public function setSeq($seq){
		$this->seq = $seq;
	}
	
	public function getSeq(){
		return $this->seq;
	}
	
	
	/* Writer IP address */
	public function setRegIp($regIp){
		$this->regIp = $regIp;
	}
	
	public function getRegIp(){
		return $this->regIp;
	}
	
	/* Writer IP address */
	public function setModIp($modIp){
		$this->modIp = $modIp;
	}
	
	public function getModIp(){
		return $this->modIp;
	}	
	
	/* Writer username */	
	public function setRegId($regId){
		$this->regId = $regId;
	}
	
	public function getRegId(){
		return $this->regId;
	}
	
	
	/* Writing date */	
	public function setRegDate($regDate){
		$this->regDate = $regDate;
	}
	
	public function getRegDate(){
		return $this->regDate;
	}
	
	
	/* Modifier Id */	
	public function setModId($modId){
		$this->modId = $modId;
	}
	
	public function getModId(){
		return $this->modId;
	}
	
	
	/* Modification date */	
	public function setModDate($modDate){
		$this->modDate = $modDate;
	}
	
	public function getModDate(){
		return $this->modDate;
	}
	
	
	/* Is deleted */	
	public function setDelYn($delYn){
		$this->delYn = $delYn;
	}
	
	public function getDelYn(){
		return $this->delYn;
	}
	
	
	
	/* Search related */	
	public function setSearchCondition($searchCondition){
		$this->searchCondition = $searchCondition;
	}
	
	public function getSearchCondition(){
		return $this->searchCondition;
	}
	
	
	public function setSearchKeyword($searchKeyword){
		$this->searchKeyword = $searchKeyword;
	}
	
	public function getSearchKeyword(){
		return $this->searchKeyword;
	}
	
	
	public function setStartRow($startRow){
		$this->startRow = $startRow;
	}
	
	public function getStartRow(){
		return $this->startRow;
	}
	
	
	public function setEndRow($endRow){
		$this->endRow = $endRow;
	}
	
	public function getEndRow(){
		return $this->endRow;
	}
	
	public function setPageSize($pageSize){
		$this->pageSize = $pageSize;
	}
	
	public function getPageSize(){
		return $this->pageSize;
	}	
	

	public function setErrors($errors){
		$this->errors = $errors;
	}
	
	public function getErrors(){
		return $this->errors;
	}	
}