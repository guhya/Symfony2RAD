<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class ServiceVO extends CommonVO{
	
	private $name;
	private $description;
	private $extra1;
	private $extra2;
	private $extra3;
	private $extra4;
	private $extra5;
	private $extra6;
	private $extra7;
	private $extra8;
	private $extra9;
	private $extra10;
	
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
	
	public function setExtra1($extra1){
		$this->extra1 = $extra1;
	}
	public function getExtra1(){
		return $this->extra1;
	}

	public function setExtra2($extra2){
		$this->extra2 = $extra2;
	}
	public function getExtra2(){
		return $this->extra2;
	}

	public function setExtra3($extra3){
		$this->extra3 = $extra3;
	}
	public function getExtra3(){
		return $this->extra3;
	}

	public function setExtra4($extra4){
		$this->extra4 = $extra4;
	}
	public function getExtra4(){
		return $this->extra4;
	}
	
	public function setExtra5($extra5){
		$this->extra5 = $extra5;
	}
	public function getExtra5(){
		return $this->extra5;
	}
	
	public function setExtra6($extra6){
		$this->extra6 = $extra6;
	}	
	public function getExtra6(){
		return $this->extra6;
	}
	
	public function setExtra7($extra7){
		$this->extra7 = $extra7;
	}	
	public function getExtra7(){
		return $this->extra7;
	}
	
	public function setExtra8($extra8){
		$this->extra8 = $extra8;
	}	
	public function getExtra8(){
		return $this->extra8;
	}
	
	public function setExtra9($extra9){
		$this->extra9 = $extra9;
	}	
	public function getExtra9(){
		return $this->extra9;
	}
	
	public function setExtra10($extra10){
		$this->extra10 = $extra10;
	}	
	public function getExtra10(){
		return $this->extra10;
	}
	
}