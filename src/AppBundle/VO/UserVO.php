<?php
namespace AppBundle\VO;

use AppBundle\VO\CommonVO; 

class UserVO extends CommonVO{
	
	private $username;
	private $password;
	private $oldPassword;
	private $firstName;
	private $lastName;
	private $email;
	
	public function setUsername($username){
		$this->username = $username;
	}	
	public function getUsername(){
		return $this->username;
	}
		
	public function setPassword($password){
		$this->password = $password;
	}	
	public function getPassword(){
		return $this->password;
	}
	
	public function setOldPassword($oldPassword){
		$this->oldPassword = $oldPassword;
	}	
	public function getOldPassword(){
		return $this->oldPassword;
	}
	
	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}
	public function getFirstName(){
		return $this->firstName;
	}

	public function setLastName($lastName){
		$this->lastName = $lastName;
	}
	public function getLastName(){
		return $this->lastName;
	}

	public function setEmail($email){
		$this->email = $email;
	}
	public function getEmail(){
		return $this->email;
	}
	
}