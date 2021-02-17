<?php
/*
	PDO class
	Connect to DB 
	Creates prepared statements
	Bind values
	Execute and return result form DB
*/

//////////////////////////////////////////////////
class Database
{
	// connection variables
	private $host = DB_HOST;
	private $user = DB_USER;
	private $password = DB_PASS;
	private $dbName = DB_NAME;

	// some local properties
	// we store out connecion
	// dbh - database handler
	private $dbh;
	private $stmt;
	private $error;

	public function __construct()
	{
		// set dns
		$dns = "mysql:host=$this->host;dbname=$this->dbName;";

		// set some connection options
		$options = [
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		];

		// create PDO instance 
		try {
			// if we have erro here
			// echo 'tyring in DB';
			// connect to DB
			$this->dbh = new PDO($dns, $this->user, $this->password, $options);
		} catch (PDOException $e) {
			// we catch error here
			$this->error = $e->getMessage();
			echo $this->error;
			// echo 'catching in DB';
		}
	}

	//////////////////////////////////////////////////
	// Prepare statments with query
	// dbh->query('SELECT * FROM posts WHERE email = :email');
	public function query($sql)
	{
		// prepare sql statment and save it in local private var
		$this->stmt = $this->dbh->prepare($sql);
	}

	//////////////////////////////////////////////////
	// use stm->bind(':email', 'john@jame.com');
	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			// check what type is $value
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		// Bind Value 
		$this->stmt->bindValue($param, $value, $type);
	}


	///////kvieciami metodai is DB modeliu klasiu//////
	//////////////////////////////////////////////////
	// execute prepared and binded stament 
	// return result
	public function execute()
	{
		return $this->stmt->execute();
	}

	//////////////////////////////////////////////////
	// Get results as an array 
	// return db result array
	public function resultSet()
	{
		$this->execute();
		// PDO::FETCH_OBJ $result[1]->id
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}

	//////////////////////////////////////////////////
	// method to return single row of data
	public function singleRow()
	{
		$this->execute();
		// PDO::FETCH_ASSOC returnina kaip masyva
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	//////////////////////////////////////////////////
	// method to get back number of rows
	public function rowCount()
	{
		return $this->stmt->rowCount();
	}

}
