<?php
namespace AppBundle\Common;

use AppBundle\VO\FileVO;
use AppBundle\Common\FileDAO;

class FileService
{

	private $dao;
	
	public function __construct(FileDAO $dao)
	{
		$this->dao = $dao;
	}

	public function insert(FileVO $fileVo)
	{
		return $this->dao->insert($fileVo);
	}

	public function delete(FileVO $fileVo)
	{
		return $this->dao->delete($fileVo);
	}

	public function getById(FileVO $fileVo)
	{
		return $this->dao->getById($fileVo);
	}
	
	public function getByOwner($channel, $ownerSeq)
	{
		return $this->dao->getByOwner($channel, $ownerSeq);
	}
	
}