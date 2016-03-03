<?php
namespace AppBundle\Common;

use Doctrine\DBAL\Connection;
use AppBundle\VO\TermRelationshipVO;

class TermRelationshipDAO
{

	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(TermRelationshipVO $termRelationshipVo)
	{
		$sql = "INSERT INTO tbTermRelationship (
						  channel
						, ownerSeq
						, termSeq
						, taxonomy
						, regIp
						, regId
						, regDate
						, delYn
					)
					VALUES(
						:channel
						, :ownerSeq
						, :termSeq
						, :taxonomy
						, :regIp
						, :regId
						, NOW()
						, 'N'
					)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("channel"		, $termRelationshipVo->getChannel());
		$stmt->bindValue("ownerSeq"		, $termRelationshipVo->getOwnerSeq());
		$stmt->bindValue("termSeq"		, $termRelationshipVo->getTermSeq());
		$stmt->bindValue("taxonomy"		, $termRelationshipVo->getTaxonomy());
		
		$stmt->bindValue("regIp"		, $termRelationshipVo->getRegIp());
		$stmt->bindValue("regId"		, $termRelationshipVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function deleteBySeq(TermRelationshipVO $termRelationshipVo)
	{
		$sql = "UPDATE tbTermRelationship SET
					delYn		= 'Y'
					, modIp		= :modIp
					, modId		= :modId
					, modDate	= NOW()
				WHERE seq 		= :seq AND ownerSeq = :ownerSeq";

		$stmt = $this->con->prepare($sql);		
		$stmt->bindValue("seq"			, $termRelationshipVo->getSeq());
		$stmt->bindValue("ownerSeq"		, $termRelationshipVo->getOwnerSeq());
		$stmt->bindValue("modIp"		, $termRelationshipVo->getModIp());
		$stmt->bindValue("modId"		, $termRelationshipVo->getModId());

		return $stmt->execute();
	}

	public function deleteByOwner(TermRelationshipVO $termRelationshipVo)
	{
		$sql = "UPDATE tbTermRelationship SET
					delYn		= 'Y'
					, modIp		= :modIp
					, modId		= :modId
					, modDate	= NOW()
				WHERE ownerSeq = :ownerSeq AND channel = :channel AND taxonomy = :taxonomy" ;
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("ownerSeq"		, $termRelationshipVo->getOwnerSeq());
		$stmt->bindValue("channel"		, $termRelationshipVo->getChannel());
		$stmt->bindValue("taxonomy"		, $termRelationshipVo->getTaxonomy());
		
		$stmt->bindValue("modIp"		, $termRelationshipVo->getModIp());
		$stmt->bindValue("modId"		, $termRelationshipVo->getModId());
	
		return $stmt->execute();
	}
	
	public function getById(TermRelationshipVO $termRelationshipVo)
	{
		$sql = "SELECT 
					seq
					, channel
					, ownerSeq
					, termSeq
					, regIp
					, regId
					, regDate
					, modId
					, modDate				
				FROM tbTermRelationship 
				WHERE seq = :seq AND delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $termRelationshipVo->getSeq());
		$stmt->execute();
		return $stmt->fetch();
	}
	
	public function getByOwner(TermRelationshipVO $termRelationshipVo)
	{
		$sql = "SELECT
					a.termSeq as seq
					, b.name
				FROM tbTermRelationship a INNER JOIN tbTerm b ON a.termSeq = b.seq
				WHERE a.taxonomy = :taxonomy AND a.channel = :channel AND a.ownerSeq = :ownerSeq AND a.delYn = 'N'
				ORDER BY b.name ASC";
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("taxonomy"		, $termRelationshipVo->getTaxonomy());
		$stmt->bindValue("channel"		, $termRelationshipVo->getChannel());
		$stmt->bindValue("ownerSeq"		, $termRelationshipVo->getOwnerSeq());
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
}