<?php
namespace AppBundle\Common;

use AppBundle\VO\TermRelationshipVO;
use AppBundle\Common\TermRelationshipDAO;

class TermRelationshipService
{

	private $dao;
	
	public function __construct(TermRelationshipDAO $dao)
	{
		$this->dao = $dao;
	}

	public function insert(TermRelationshipVO $termRelationshipVo)
	{
		return $this->dao->insert($termRelationshipVo);
	}

	public function delete(TermRelationshipVO $termRelationshipVo)
	{
		return $this->dao->delete($termRelationshipVo);
	}

	public function getById(TermRelationshipVO $termRelationshipVo)
	{
		return $this->dao->getById($termRelationshipVo);
	}
	
	public function getByOwner($channel, $ownerSeq)
	{
		return $this->dao->getByOwner($channel, $ownerSeq);
	}
	
}