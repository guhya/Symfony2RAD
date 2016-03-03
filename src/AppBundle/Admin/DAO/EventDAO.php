<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\EventVO;

class EventDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(EventVO $eventVo)
	{
		$sql = "INSERT INTO tbEvent(
					title
					, content
					, startDate
					, endDate
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:title
					, :content
					, :startDate
					, :endDate
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("title"		, $eventVo->getTitle());
		$stmt->bindValue("content"		, $eventVo->getContent());
		$stmt->bindValue("startDate"	, $eventVo->getStartDate());
		$stmt->bindValue("endDate"		, $eventVo->getEndDate());

		$stmt->bindValue("regIp"		, $eventVo->getRegIp());
		$stmt->bindValue("regId"		, $eventVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(EventVO $eventVo)
	{
		$sql = "UPDATE tbEvent SET
					title			= :title
					, content		= :content
					, startDate		= :startDate
					, endDate		= :endDate
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $eventVo->getSeq());

		$stmt->bindValue("title"		, $eventVo->getTitle());
		$stmt->bindValue("content"		, $eventVo->getContent());
		$stmt->bindValue("startDate"	, $eventVo->getStartDate());
		$stmt->bindValue("endDate"		, $eventVo->getEndDate());
		
		$stmt->bindValue("modIp"		, $eventVo->getModIp());
		$stmt->bindValue("modId"		, $eventVo->getModId());

		return $stmt->execute();
	}

	public function delete(EventVO $eventVo)
	{
		$sql = "UPDATE tbEvent SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $eventVo->getSeq());
		$stmt->bindValue("modIp"		, $eventVo->getModIp());
		$stmt->bindValue("modId"		, $eventVo->getModId());

		return $stmt->execute();
	}

	public function getById(EventVO $eventVo)
	{
		$sql = "SELECT
					a.seq
					, a.title
					, a.content
					, a.startDate
					, a.endDate
					, a.regIp
					, a.regId
					, a.regDate
					, a.modIp
					, a.modId
					, a.modDate
					, b1.seq AS thumbnailImage
					, b1.originalName AS thumbnailImageOriginalName
					, b2.seq AS mainImage
					, b2.originalName AS mainImageOriginalName
					, b7.seq AS attachment
					, b7.originalName AS attachmentOriginalName
					, c1a.seq AS category
					, c1a.name AS categoryName
				FROM tbEvent a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'event' AND b1.category = 'thumbnailImage' 
					LEFT JOIN tbFile b2 ON a.seq = b2.ownerSeq AND b2.delYn = 'N' AND b2.channel = 'event' AND b2.category = 'mainImage' 
					LEFT JOIN tbFile b7 ON a.seq = b7.ownerSeq AND b7.delYn = 'N' AND b7.channel = 'event' AND b7.category = 'attachment' 
					LEFT JOIN tbTermRelationship c1 INNER JOIN tbTerm c1a 
						ON c1.termSeq = c1a.seq ON a.seq = c1.ownerSeq AND c1.delYn = 'N' AND c1a.delYn = 'N' AND c1.taxonomy = 'cat' AND c1.channel = 'event' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $eventVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(EventVO $eventVo)
	{
		$c = $eventVo->getSearchCondition();
		$k = $eventVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND a.delYn = 'N' ";
		if ($c){
			if ($c == "title"){
				$c = "a.title";
			}else{
				$c = "a.content";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT 
					a.seq, 
					a.title,				
					a.content,
					a.startDate,
					a.endDate,
					b.seq AS categorySeq,
					b1.name AS category
				FROM tbEvent a 
				LEFT JOIN tbTermRelationship b INNER JOIN tbTerm b1
					ON b.termSeq = b1.seq ON a.seq = b.ownerSeq AND b.delYn = 'N' AND b1.delYn = 'N' AND b.taxonomy = 'cat' AND b.channel='event' 
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $eventVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $eventVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(EventVO $eventVo)
	{
		$c = $eventVo->getSearchCondition();
		$k = $eventVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "title"){
				$c = "title";
			}else{
				$c = "content";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbEvent ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function setViewCount(EventVO $eventVo)
	{
		$sql = "UPDATE tbEvent SET viewCount = viewCount + 1 WHERE seq 	= :seq";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"	, $eventVo->getSeq());

		return $stmt->execute();
	}
}
