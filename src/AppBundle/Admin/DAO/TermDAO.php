<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\TermVO;

class TermDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(TermVO $termVo)
	{
		$sql = "INSERT INTO tbTerm(
					name
					, description
					, taxonomy
					, parent
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:name
					, :description
					, :taxonomy
					, :parent
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("name"			, $termVo->getName());
		$stmt->bindValue("description"	, $termVo->getDescription());
		$stmt->bindValue("taxonomy"		, $termVo->getTaxonomy());
		$stmt->bindValue("parent"		, $termVo->getParent());

		$stmt->bindValue("regIp"		, $termVo->getRegIp());
		$stmt->bindValue("regId"		, $termVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function insertParentCategory(TermVO $termVo)
	{
		$sql = "INSERT INTO tbTerm(
					name
					, description
					, taxonomy
					, parent
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:name
					, :description
					, :taxonomy
					, 0
					, :regIp
					, :regId
					, NOW()
					, 'N'
				);				
				UPDATE tbTerm SET 
					lineage = CONCAT(LPAD(LAST_INSERT_ID(), 4, '0'), '/') 
				WHERE seq = LAST_INSERT_ID();
				";
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("name"			, $termVo->getName());
		$stmt->bindValue("description"	, $termVo->getDescription());
		$stmt->bindValue("taxonomy"		, $termVo->getTaxonomy());
	
		$stmt->bindValue("regIp"		, $termVo->getRegIp());
		$stmt->bindValue("regId"		, $termVo->getRegId());
		$stmt->execute();
	
		return $this->con->lastInsertId();
	}	
	
	public function insertChildCategory(TermVO $termVo)
	{
		$sql = "INSERT INTO tbTerm(
					name
					, description
					, taxonomy
					, parent
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:name
					, :description
					, :taxonomy
					, :parent
					, :regIp
					, :regId
					, NOW()
					, 'N'
				);
				UPDATE tbTerm SET 
					lineage = CONCAT((SELECT lineage FROM (SELECT lineage FROM tbTerm a WHERE a.seq = :parent) AS x), LPAD(LAST_INSERT_ID(), 4, '0'), '/') 
				WHERE seq = LAST_INSERT_ID();
				";
				
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("name"			, $termVo->getName());
		$stmt->bindValue("description"	, $termVo->getDescription());
		$stmt->bindValue("taxonomy"		, $termVo->getTaxonomy());
		$stmt->bindValue("parent"		, $termVo->getParent());
	
		$stmt->bindValue("regIp"		, $termVo->getRegIp());
		$stmt->bindValue("regId"		, $termVo->getRegId());
		$stmt->execute();
	
		return $this->con->lastInsertId();
	}
	
	public function update(TermVO $termVo)
	{
		$sql = "UPDATE tbTerm SET
					name			= :name
					, description	= :description
					, taxonomy		= :taxonomy
					, parent		= :parent
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $termVo->getSeq());

		$stmt->bindValue("name"			, $termVo->getName());
		$stmt->bindValue("description"	, $termVo->getDescription());
		$stmt->bindValue("taxonomy"		, $termVo->getTaxonomy());
		$stmt->bindValue("parent"		, $termVo->getParent());

		$stmt->bindValue("modIp"		, $termVo->getModIp());
		$stmt->bindValue("modId"		, $termVo->getModId());

		return $stmt->execute();
	}

	public function delete(TermVO $termVo)
	{
		$sql = "UPDATE tbTerm SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE lineage LIKE :lineage
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("lineage"		, $termVo->getLineage().'/%');
		$stmt->bindValue("modIp"		, $termVo->getModIp());
		$stmt->bindValue("modId"		, $termVo->getModId());

		return $stmt->execute();
	}

	public function getById(TermVO $termVo)
	{
		$sql = "SELECT
					a.seq
					, a.name
					, a.description
					, a.taxonomy
					, a.parent
					, a.lineage
					, a.regIp
					, a.regId
					, a.regDate
					, a.modIp
					, a.modId
					, a.modDate
				FROM tbTerm a
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $termVo->getSeq());
		$stmt->execute();
		return $stmt->fetch();
	}

	public function getList(TermVO $termVo)
	{
		$c = $termVo->getSearchCondition();
		$k = $termVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "name"){
				$c = "name";
			}else{
				$c = "description";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT 
				seq, name, description, taxonomy, lineage
				FROM tbTerm ".$sWhere." ORDER BY seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $termVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $termVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(TermVO $termVo)
	{
		$c = $termVo->getSearchCondition();
		$k = $termVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "name"){
				$c = "name";
			}else{
				$c = "description";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbTerm ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}
	
	public function getTags()
	{
		$sql = "SELECT seq, name FROM tbTerm WHERE delYn = 'N' AND taxonomy = 'tag' ORDER BY name ASC";
		
		$stmt = $this->con->prepare($sql);
		$stmt->execute();
		
		return $stmt->fetchAll();
	}
	
	public function getCategories()
	{
		$sql = "SELECT 
					*,
					REPEAT('-', CAST((LENGTH(lineage)/5) AS UNSIGNED)*5) as padding
				FROM tbTerm 
				WHERE delYn = 'N' AND taxonomy = 'cat'
				ORDER BY lineage ASC";
	
		$stmt = $this->con->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getCategoriesByParent(TermVO $termVo)
	{
		$sql = "SELECT 
					*,
					REPEAT('-', CAST((LENGTH(lineage)/5) AS UNSIGNED)) as padding
				FROM tbTerm 
				WHERE delYn = 'N' AND taxonomy = 'cat' AND lineage LIKE CONCAT(LPAD(':seq', 4, '0'), '/%')  
				ORDER BY lineage ASC";
	
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq", $termVo->getSeq());
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	public function getFullPath($seq){
		$sql = "SELECT b.seq, b.name FROM tbTerm b WHERE LOCATE(b.seq, (SELECT a.lineage FROM tbTerm	a WHERE a.seq=:seq)) > 0 ORDER BY b.seq ASC;";
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq", $seq);
		$stmt->execute();
		return $stmt->fetchAll();		
	}
	
}
