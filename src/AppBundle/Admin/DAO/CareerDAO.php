<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\CareerVO;

class CareerDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(CareerVO $careerVo)
	{
		$sql = "INSERT INTO tbCareer(
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
		$stmt->bindValue("title"		, $careerVo->getTitle());
		$stmt->bindValue("content"		, $careerVo->getContent());
		$stmt->bindValue("startDate"	, $careerVo->getStartDate());
		$stmt->bindValue("endDate"		, $careerVo->getEndDate());

		$stmt->bindValue("regIp"		, $careerVo->getRegIp());
		$stmt->bindValue("regId"		, $careerVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(CareerVO $careerVo)
	{
		$sql = "UPDATE tbCareer SET
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
		$stmt->bindValue("seq"			, $careerVo->getSeq());

		$stmt->bindValue("title"		, $careerVo->getTitle());
		$stmt->bindValue("content"		, $careerVo->getContent());
		$stmt->bindValue("startDate"	, $careerVo->getStartDate());
		$stmt->bindValue("endDate"		, $careerVo->getEndDate());
		
		$stmt->bindValue("modIp"		, $careerVo->getModIp());
		$stmt->bindValue("modId"		, $careerVo->getModId());

		return $stmt->execute();
	}

	public function delete(CareerVO $careerVo)
	{
		$sql = "UPDATE tbCareer SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $careerVo->getSeq());
		$stmt->bindValue("modIp"		, $careerVo->getModIp());
		$stmt->bindValue("modId"		, $careerVo->getModId());

		return $stmt->execute();
	}

	public function getById(CareerVO $careerVo)
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
				FROM tbCareer a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'career' AND b1.category = 'thumbnailImage' 
					LEFT JOIN tbFile b2 ON a.seq = b2.ownerSeq AND b2.delYn = 'N' AND b2.channel = 'career' AND b2.category = 'mainImage' 
					LEFT JOIN tbFile b7 ON a.seq = b7.ownerSeq AND b7.delYn = 'N' AND b7.channel = 'career' AND b7.category = 'attachment' 
					LEFT JOIN tbTermRelationship c1 INNER JOIN tbTerm c1a 
						ON c1.termSeq = c1a.seq ON a.seq = c1.ownerSeq AND c1.delYn = 'N' AND c1a.delYn = 'N' AND c1.taxonomy = 'cat' AND c1.channel = 'career' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $careerVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(CareerVO $careerVo)
	{
		$c = $careerVo->getSearchCondition();
		$k = $careerVo->getSearchKeyword();

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
				FROM tbCareer a 
				LEFT JOIN tbTermRelationship b INNER JOIN tbTerm b1
					ON b.termSeq = b1.seq ON a.seq = b.ownerSeq AND b.delYn = 'N' AND b1.delYn = 'N' AND b.taxonomy = 'cat' AND b.channel='career' 
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $careerVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $careerVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(CareerVO $careerVo)
	{
		$c = $careerVo->getSearchCondition();
		$k = $careerVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "title"){
				$c = "title";
			}else{
				$c = "content";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbCareer ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function setViewCount(CareerVO $careerVo)
	{
		$sql = "UPDATE tbCareer SET viewCount = viewCount + 1 WHERE seq 	= :seq";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"	, $careerVo->getSeq());

		return $stmt->execute();
	}
}
