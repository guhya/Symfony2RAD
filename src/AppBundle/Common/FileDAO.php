<?php
namespace AppBundle\Common;

use Doctrine\DBAL\Connection;
use AppBundle\VO\FileVO;

class FileDAO
{

	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(FileVO $fileVo)
	{
		$sql = "INSERT INTO tbFile (
						  channel
						, category
						, ownerSeq
						, name
						, originalName
						, size
						, path
						, regIp
						, regId
						, regDate
						, delYn
					)
					VALUES(
						:channel
						, :category
						, :ownerSeq
						, :name
						, :originalName
						, :size
						, :path
						, :regIp
						, :regId
						, NOW()
						, 'N'
					)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("category"		, $fileVo->getCategory());
		$stmt->bindValue("channel"		, $fileVo->getChannel());
		$stmt->bindValue("ownerSeq"		, $fileVo->getOwnerSeq());
		$stmt->bindValue("name"			, $fileVo->getName());
		$stmt->bindValue("originalName"	, $fileVo->getOriginalName());
		$stmt->bindValue("size"			, $fileVo->getSize());
		$stmt->bindValue("path"			, $fileVo->getPath());
		$stmt->bindValue("regIp"		, $fileVo->getRegIp());
		$stmt->bindValue("regId"		, $fileVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function delete(FileVO $fileVo)
	{
		$sql = "UPDATE tbFile SET
					delYn		= 'Y'
					, modIp		= :modIp
					, modId		= :modId
					, modDate	= NOW()
				WHERE seq 		= :seq AND ownerSeq = :ownerSeq";

		$stmt = $this->con->prepare($sql);		
		$stmt->bindValue("seq"			, $fileVo->getSeq());
		$stmt->bindValue("ownerSeq"		, $fileVo->getOwnerSeq());
		$stmt->bindValue("modIp"		, $fileVo->getModIp());
		$stmt->bindValue("modId"		, $fileVo->getModId());

		return $stmt->execute();
	}

	public function getById(FileVO $fileVo)
	{
		$sql = "SELECT 
					seq
					, category
					, channel
					, ownerSeq
					, name
					, originalName
					, size
					, path
					, regIp
					, regId
					, regDate
					, modId
					, modDate				
				FROM tbFile 
				WHERE seq = :seq AND delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $fileVo->getSeq());
		$stmt->execute();
		return $stmt->fetch();
	}
	
	public function getByOwner($channel, $ownerSeq)
	{
		$sql = "SELECT
					seq
					, category
					, channel
					, ownerSeq
					, name
					, originalName
					, size
					, path
					, regIp
					, regId
					, regDate
					, modId
					, modDate
				FROM tbFile
				WHERE channel = :channel AND ownerSeq = :ownerSeq AND delYn = 'N'";
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("channel"		, $channel);
		$stmt->bindValue("ownerSeq"		, $ownerSeq);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
}