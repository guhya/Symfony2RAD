<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\NewsVO;

class NewsDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(NewsVO $newsVo)
	{
		$sql = "INSERT INTO tbNews(
					title
					, content
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:title
					, :content
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("title"		, $newsVo->getTitle());
		$stmt->bindValue("content"		, $newsVo->getContent());

		$stmt->bindValue("regIp"		, $newsVo->getRegIp());
		$stmt->bindValue("regId"		, $newsVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(NewsVO $newsVo)
	{
		$sql = "UPDATE tbNews SET
					title			= :title
					, content		= :content
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $newsVo->getSeq());

		$stmt->bindValue("title"		, $newsVo->getTitle());
		$stmt->bindValue("content"		, $newsVo->getContent());

		$stmt->bindValue("modIp"		, $newsVo->getModIp());
		$stmt->bindValue("modId"		, $newsVo->getModId());

		return $stmt->execute();
	}

	public function delete(NewsVO $newsVo)
	{
		$sql = "UPDATE tbNews SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $newsVo->getSeq());
		$stmt->bindValue("modIp"		, $newsVo->getModIp());
		$stmt->bindValue("modId"		, $newsVo->getModId());

		return $stmt->execute();
	}

	public function getById(NewsVO $newsVo)
	{
		$sql = "SELECT
					a.seq
					, a.title
					, a.content
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
				FROM tbNews a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'news' AND b1.category = 'thumbnailImage' 
					LEFT JOIN tbFile b2 ON a.seq = b2.ownerSeq AND b2.delYn = 'N' AND b2.channel = 'news' AND b2.category = 'mainImage' 
					LEFT JOIN tbFile b7 ON a.seq = b7.ownerSeq AND b7.delYn = 'N' AND b7.channel = 'news' AND b7.category = 'attachment' 
					LEFT JOIN tbTermRelationship c1 INNER JOIN tbTerm c1a 
						ON c1.termSeq = c1a.seq ON a.seq = c1.ownerSeq AND c1.delYn = 'N' AND c1a.delYn = 'N' AND c1.taxonomy = 'cat' AND c1.channel = 'news' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $newsVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(NewsVO $newsVo)
	{
		$c = $newsVo->getSearchCondition();
		$k = $newsVo->getSearchKeyword();

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
					b.seq AS categorySeq,
					b1.name AS category
				FROM tbNews a 
				LEFT JOIN tbTermRelationship b INNER JOIN tbTerm b1
					ON b.termSeq = b1.seq ON a.seq = b.ownerSeq AND b.delYn = 'N' AND b1.delYn = 'N' AND b.taxonomy = 'cat' AND b.channel='news'
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $newsVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $newsVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(NewsVO $newsVo)
	{
		$c = $newsVo->getSearchCondition();
		$k = $newsVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "title"){
				$c = "title";
			}else{
				$c = "content";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbNews ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function setViewCount(NewsVO $newsVo)
	{
		$sql = "UPDATE tbNews SET viewCount = viewCount + 1 WHERE seq 	= :seq";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"	, $newsVo->getSeq());

		return $stmt->execute();
	}
}
