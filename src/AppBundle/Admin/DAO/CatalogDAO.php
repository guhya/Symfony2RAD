<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\CatalogVO;

class CatalogDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(CatalogVO $catalogVo)
	{
		$sql = "INSERT INTO tbCatalog(
					name
					, description
					, url
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:name
					, :description
					, :url
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("name"			, $catalogVo->getName());
		$stmt->bindValue("description"	, $catalogVo->getDescription());
		$stmt->bindValue("url"			, $catalogVo->getUrl());

		$stmt->bindValue("regIp"		, $catalogVo->getRegIp());
		$stmt->bindValue("regId"		, $catalogVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(CatalogVO $catalogVo)
	{
		$sql = "UPDATE tbCatalog SET
					name			= :name
					, description	= :description
					, url			= :url
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $catalogVo->getSeq());

		$stmt->bindValue("name"			, $catalogVo->getName());
		$stmt->bindValue("description"	, $catalogVo->getDescription());
		$stmt->bindValue("url"			, $catalogVo->getUrl());

		$stmt->bindValue("modIp"		, $catalogVo->getModIp());
		$stmt->bindValue("modId"		, $catalogVo->getModId());

		return $stmt->execute();
	}

	public function delete(CatalogVO $catalogVo)
	{
		$sql = "UPDATE tbCatalog SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $catalogVo->getSeq());
		$stmt->bindValue("modIp"		, $catalogVo->getModIp());
		$stmt->bindValue("modId"		, $catalogVo->getModId());

		return $stmt->execute();
	}

	public function getById(CatalogVO $catalogVo)
	{
		$sql = "SELECT
					a.seq
					, a.name
					, a.description
					, a.url
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
				FROM tbCatalog a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'catalog' AND b1.category = 'thumbnailImage' 
					LEFT JOIN tbFile b2 ON a.seq = b2.ownerSeq AND b2.delYn = 'N' AND b2.channel = 'catalog' AND b2.category = 'mainImage' 
					LEFT JOIN tbFile b7 ON a.seq = b7.ownerSeq AND b7.delYn = 'N' AND b7.channel = 'catalog' AND b7.category = 'attachment' 
					LEFT JOIN tbTermRelationship c1 INNER JOIN tbTerm c1a 
						ON c1.termSeq = c1a.seq ON a.seq = c1.ownerSeq AND c1.delYn = 'N' AND c1a.delYn = 'N' AND c1.taxonomy = 'cat' AND c1.channel = 'catalog' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $catalogVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(CatalogVO $catalogVo)
	{
		$c = $catalogVo->getSearchCondition();
		$k = $catalogVo->getSearchKeyword();

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
				FROM tbCatalog a 
				LEFT JOIN tbTermRelationship b INNER JOIN tbTerm b1
					ON b.termSeq = b1.seq ON a.seq = b.ownerSeq AND b.delYn = 'N' AND b1.delYn = 'N' AND b.taxonomy = 'cat' AND b.channel='catalog'
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $catalogVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $catalogVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(CatalogVO $catalogVo)
	{
		$c = $catalogVo->getSearchCondition();
		$k = $catalogVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "name"){
				$c = "name";
			}else{
				$c = "description";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbCatalog ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function setViewCount(CatalogVO $catalogVo)
	{
		$sql = "UPDATE tbCatalog SET viewCount = viewCount + 1 WHERE seq 	= :seq";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"	, $catalogVo->getSeq());

		return $stmt->execute();
	}
}
