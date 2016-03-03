<?php
namespace AppBundle\Admin\DAO;

use Doctrine\DBAL\Connection;
use AppBundle\VO\UserVO;

class UserDAO
{
	private $con;

	public function __construct(Connection $con)
	{
		$this->con = $con;
	}

	public function insert(UserVO $userVo)
	{
		$sql = "INSERT INTO tbUser(
					username
					, password
					, firstName
					, lastName
					, email
					, regIp
					, regId
					, regDate
					, delYn
				)
				VALUES(
					:username
					, :password
					, :firstName
					, :lastName
					, :email
					, :regIp
					, :regId
					, NOW()
					, 'N'
				)";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("username"		, $userVo->getUsername());
		$stmt->bindValue("password"		, $userVo->getPassword());
		$stmt->bindValue("firstName"	, $userVo->getFirstName());
		$stmt->bindValue("lastName"		, $userVo->getLastName());
		$stmt->bindValue("email"		, $userVo->getEmail());

		$stmt->bindValue("regIp"		, $userVo->getRegIp());
		$stmt->bindValue("regId"		, $userVo->getRegId());
		$stmt->execute();

		return $this->con->lastInsertId();
	}

	public function update(UserVO $userVo)
	{
		$sql = "UPDATE tbUser SET
					username		= :username
					, password		= :password
					, firstName		= :firstName
					, lastName		= :lastName
					, email			= :email
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $userVo->getSeq());

		$stmt->bindValue("username"		, $userVo->getUsername());
		$stmt->bindValue("password"		, $userVo->getPassword());
		$stmt->bindValue("firstName"	, $userVo->getFirstName());
		$stmt->bindValue("lastName"		, $userVo->getLastName());
		$stmt->bindValue("email"		, $userVo->getEmail());

		$stmt->bindValue("modIp"		, $userVo->getModIp());
		$stmt->bindValue("modId"		, $userVo->getModId());

		return $stmt->execute();
	}

	public function delete(UserVO $userVo)
	{
		$sql = "UPDATE tbUser SET
					delYn			= 'Y'
					, modIp			= :modIp
					, modId			= :modId
					, modDate		= NOW()
				WHERE seq 			= :seq
				";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"			, $userVo->getSeq());
		$stmt->bindValue("modIp"		, $userVo->getModIp());
		$stmt->bindValue("modId"		, $userVo->getModId());

		return $stmt->execute();
	}

	public function getById(UserVO $userVo)
	{
		$sql = "SELECT
					a.seq
					, a.username
					, a.password
					, a.firstName
					, a.lastName
					, a.email
					, a.regIp
					, a.regId
					, a.regDate
					, a.modIp
					, a.modId
					, a.modDate
					, b1.seq AS thumbnailImage
					, b1.originalName AS thumbnailImageOriginalName
				FROM tbUser a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'user' AND b1.category = 'thumbnailImage' 
				WHERE a.seq = :seq AND a.delYn = 'N'";

		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("seq"		, $userVo->getSeq());
		$stmt->execute();
		
		return $stmt->fetch();
	}

	public function getList(UserVO $userVo)
	{
		$c = $userVo->getSearchCondition();
		$k = $userVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND a.delYn = 'N' ";
		if ($c){
			if ($c == "username"){
				$c = "a.username";
			}else{
				$c = "a.password";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT 
					a.seq, 
					a.username,
					a.password,
					a.firstName,
					a.lastName
				FROM tbUser a 
				".$sWhere." ORDER BY a.seq DESC LIMIT :startRow, :pageSize";

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->bindValue("startRow"		, $userVo->getStartRow(), \PDO::PARAM_INT);
		$stmt->bindValue("pageSize"		, $userVo->getPageSize(), \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function countList(UserVO $userVo)
	{
		$c = $userVo->getSearchCondition();
		$k = $userVo->getSearchKeyword();

		$sWhere = "WHERE 1=1 AND delYn = 'N' ";
		if ($c){
			if ($c == "username"){
				$c = "username";
			}else{
				$c = "password";
			}
				
			$sWhere .= " AND ".$c." LIKE :keyword";
		}

		$sql = "SELECT COUNT(seq) FROM tbUser ".$sWhere;

		$stmt = $this->con->prepare($sql);

		if ($c && isset($k)){
			$stmt->bindValue("keyword"		, "%".$k."%");
		}

		$stmt->execute();
		return $stmt->fetchColumn();
	}
	
	public function getByUsername(UserVO $userVo){
		$sql = "SELECT
					a.seq
					, a.username
					, a.password
					, a.firstName
					, a.lastName
					, a.email
					, a.regIp
					, a.regId
					, a.regDate
					, a.modIp
					, a.modId
					, a.modDate
					, b1.seq AS thumbnailImage
					, b1.originalName AS thumbnailImageOriginalName
				FROM tbUser a
					LEFT JOIN tbFile b1 ON a.seq = b1.ownerSeq AND b1.delYn = 'N' AND b1.channel = 'user' AND b1.category = 'thumbnailImage'
				WHERE a.username = :username AND a.password = :password AND a.delYn = 'N'";
		
		$stmt = $this->con->prepare($sql);
		$stmt->bindValue("username"		, $userVo->getUsername());
		$stmt->bindValue("password"		, $userVo->getPassword());
		$stmt->execute();
		
		return $stmt->fetch();
	}

}
