<?php
namespace AppBundle\Admin\Service;

use AppBundle\Admin\DAO\UserDAO;
use AppBundle\VO\UserVO;
use AppBundle\Common\VOHelper;
use AppBundle\Common\FileDAO;
use AppBundle\Common\Constants;
use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class UserService
{
	
	private $dao;
	private $fileDao;
	
	public function __construct(UserDAO $dao, FileDAO $fileDao)
	{
		$this->dao 					= $dao;
		$this->fileDao 				= $fileDao;
	}
	
	public function insert(UserVO $userVo, $fileList)
	{
		$r = $this->dao->insert($userVo);		
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$userVo->getRegIp(), $userVo->getRegId(),
													$file, Constants::USER, $key, Constants::UPLOAD_USER_PATH
													);
			$fileVo->setOwnerSeq($r);
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_USER_PATH, $fileVo->getName());
						
					$fileDeleteResult 	= $this->fileDao->delete($fileVo);
					$fileSaveResult 	= $this->fileDao->insert($fileVo);
				}
			}
		}
		
		return $r;
	}
	
	public function update(UserVO $userVo, $fileList)
	{
		$r = $this->dao->update($userVo);
		
		foreach($fileList as $key=>$file){
			$fileVo 	= VOHelper::prepareFileVo(
													$userVo->getRegIp(), $userVo->getRegId(),
													$file, Constants::USER, $key, Constants::UPLOAD_USER_PATH
													);
			$fileVo->setOwnerSeq($userVo->getSeq());
			if($r){
				if($file){
					$file->move(Constants::WEBROOT_PHYSICAL.Constants::UPLOAD_USER_PATH, $fileVo->getName());
		
					$fdr = $this->fileDao->delete($fileVo);
					$fsr = $this->fileDao->insert($fileVo);
				}
			}
		}
		
		return $r;
	}
	
	public function delete(UserVO $userVo)
	{
		return $this->dao->delete($userVo);
	}
	
	public function getById(UserVO $userVo)
	{
		$r = $this->dao->getById($userVo);
		
		return $r;
	}

	public function getList(UserVO $userVo)
	{
		return $this->dao->getList($userVo);
	}
	
	public function countList(UserVO $userVo)
	{
		return $this->dao->countList($userVo);
	}
	
	public function getByUsername(UserVO $userVo)
	{
		return $this->dao->getByUsername($userVo);
	}
	
	public function loginByUsername(UserVO $userVo)
	{
		$r = $this->dao->getByUsername($userVo);
		$isPasswordMatch = $r["password"] == $userVo->getPassword();
		
		if($isPasswordMatch){
			return $r;
		}else{
			return false;
		}
	}
}
