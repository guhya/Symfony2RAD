<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\ServiceVO;

class ServiceDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(ServiceVO $serviceVo)
	{
		$sql = "INSERT INTO tbService(
					name
					, description
					, extra1
					, extra2
					, extra3
					, extra4
					, extra5
					, extra6
					, extra7
					, extra8
					, extra9
					, extra10
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:name
					, :description
					, :extra1
					, :extra2
					, :extra3
					, :extra4
					, :extra5
					, :extra6
					, :extra7
					, :extra8
					, :extra9
					, :extra10
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("name"			, $serviceVo->getName());
		$stmt->bindValue("description"	, $serviceVo->getDescription());
		$stmt->bindValue("extra1"		, $serviceVo->getExtra1());
		$stmt->bindValue("extra2"		, $serviceVo->getExtra2());
		$stmt->bindValue("extra3"		, $serviceVo->getExtra3());
		$stmt->bindValue("extra4"		, $serviceVo->getExtra4());
		$stmt->bindValue("extra5"		, $serviceVo->getExtra5());
		$stmt->bindValue("extra6"		, $serviceVo->getExtra6());
		$stmt->bindValue("extra7"		, $serviceVo->getExtra7());
		$stmt->bindValue("extra8"		, $serviceVo->getExtra8());
		$stmt->bindValue("extra9"		, $serviceVo->getExtra9());
		$stmt->bindValue("extra10"		, $serviceVo->getExtra10());

		$stmt->bindValue("regIp"		, $serviceVo->getRegIp());
		$stmt->bindValue("regId"		, $serviceVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(ServiceVO $serviceVo)
	{
		$sql = "UPDATE tbService SET
					name			= :name
					, description	= :description
					, extra1		= :extra1
					, extra2		= :extra2
					, extra3		= :extra3
					, extra4		= :extra4
					, extra5		= :extra5
					, extra6		= :extra6
					, extra7		= :extra7
					, extra8		= :extra8
					, extra9		= :extra9
					, extra10		= :extra10
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $serviceVo->getSeq());

		$stmt->bindValue("name"			, $serviceVo->getName());
		$stmt->bindValue("description"	, $serviceVo->getDescription());
		$stmt->bindValue("extra1"		, $serviceVo->getExtra1());
		$stmt->bindValue("extra2"		, $serviceVo->getExtra2());
		$stmt->bindValue("extra3"		, $serviceVo->getExtra3());
		$stmt->bindValue("extra4"		, $serviceVo->getExtra4());
		$stmt->bindValue("extra5"		, $serviceVo->getExtra5());
		$stmt->bindValue("extra6"		, $serviceVo->getExtra6());
		$stmt->bindValue("extra7"		, $serviceVo->getExtra7());
		$stmt->bindValue("extra8"		, $serviceVo->getExtra8());
		$stmt->bindValue("extra9"		, $serviceVo->getExtra9());
		$stmt->bindValue("extra10"		, $serviceVo->getExtra10());

		$stmt->bindValue("modIp"		, $serviceVo->getModIp());
		$stmt->bindValue("modId"		, $serviceVo->getModId());

		return $stmt->execute();
	}

	public function delete(ServiceVO $serviceVo)
	{
		$sql = "UPDATE tbService SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $serviceVo->getSeq());
		$stmt->bindValue("modIp"		, $serviceVo->getModIp());
		$stmt->bindValue("modId"		, $serviceVo->getModId());

		return $stmt->execute();
	}

	public function getById(ServiceVO $serviceVo)
	{
		$sql = "SELECT
					a.seq
					, a.name
					, a.description
					, a.extra1
					, a.extra2
					, a.extra3
					, a.extra4
					, a.extra5
					, a.extra6
					, a.extra7
					, a.extra8
					, a.extra9
					, a.extra10
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
					, b3.seq AS image1
					, b3.originalName AS image1OriginalName
					, b4.seq AS image2
					, b4.originalName AS image2OriginalName
					, b5.seq AS image3
					, b5.originalName AS image3originalName
					, b6.seq AS brochure
					, b6.originalName AS brochureOriginalName
					, b7.seq AS attachment
					, b7.originalName AS attachmentOriginalName
					, c1a.seq AS category
					, c1a.name AS categoryName
				FROM tbService a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'service' AND b1.category = 'thumbnailImage' 
					LEFT JOIN tbFile b2 ON a.seq = b2.ownerSeq AND b2.delYn = 'N' AND b2.channel = 'service' AND b2.category = 'mainImage' 
					LEFT JOIN tbFile b3 ON a.seq = b3.ownerSeq AND b3.delYn = 'N' AND b3.channel = 'service' AND b3.category = 'image1' 
					LEFT JOIN tbFile b4 ON a.seq = b4.ownerSeq AND b4.delYn = 'N' AND b4.channel = 'service' AND b4.category = 'image2' 
					LEFT JOIN tbFile b5 ON a.seq = b5.ownerSeq AND b5.delYn = 'N' AND b5.channel = 'service' AND b5.category = 'image3' 
					LEFT JOIN tbFile b6 ON a.seq = b6.ownerSeq AND b6.delYn = 'N' AND b6.channel = 'service' AND b6.category = 'brochure' 
					LEFT JOIN tbFile b7 ON a.seq = b7.ownerSeq AND b7.delYn = 'N' AND b7.channel = 'service' AND b7.category = 'attachment' 
					LEFT JOIN tbTermRelationship c1 INNER JOIN tbTerm c1a 
						ON c1.termSeq = c1a.seq ON a.seq = c1.ownerSeq AND c1.delYn = 'N' AND c1a.delYn = 'N' AND c1.taxonomy = 'cat' AND c1.channel = 'service' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $serviceVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(ServiceVO $serviceVo)
	{
		$c = $serviceVo->getSearchCondition();
		$k = $serviceVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND a.delYn = 'N' ";
		if ($c){
			if ($c == "name"){
				$c = "a.name";
			}else{
				$c = "a.description";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT 
					a.seq, 
					a.name,
					a.description,
					b.seq AS categorySeq,
					b1.name AS category
				FROM tbService a 
				LEFT JOIN tbTermRelationship b INNER JOIN tbTerm b1
					ON b.termSeq = b1.seq ON a.seq = b.ownerSeq AND b.delYn = 'N' AND b1.delYn = 'N' AND b.taxonomy = 'cat' AND b.channel='service'
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $serviceVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $serviceVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(ServiceVO $serviceVo)
	{
		$c = $serviceVo->getSearchCondition();
		$k = $serviceVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "name"){
				$c = "name";
			}else{
				$c = "description";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbService ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function setViewCount(ServiceVO $serviceVo)
	{
		$sql = "UPDATE tbService SET viewCount = viewCount + 1 WHERE seq 	= :seq";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"	, $serviceVo->getSeq());

		return $stmt->execute();
	}
}
