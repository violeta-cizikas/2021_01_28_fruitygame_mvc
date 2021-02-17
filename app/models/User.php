<?php
// User class 
// for getting and setting database values 

class User
{
	private $db;

	public function __construct()
	{
		$this->db = new Database;
	}

	//////////////////////////////////////////////////
	// finds user by given nickname
	// @return Boolean
	public function findUserByNickname($nickname)
	{
		// check if the given email is in data base
		// prepare statement
		$this->db->query("SELECT * FROM users WHERE `nickname` = :nickname");

		// add values to statment
		$this->db->bind(':nickname', $nickname);

		// save result in $row
		$row = $this->db->singleRow();

		// check if we got some results
		if ($this->db->rowCount() > 0) {
			return $row;
		} else {
			return false;
		}
	}

	//////////////////////////////////////////////////
	// Register user with given sanitized data 
	// @return Boolean 
	public function register($data)
	{
		// prepare statment
		$this->db->query("INSERT INTO users (`nickname`,  `password`, `balance`) VALUES (:nickname, :password, :balance)");
		// add values
		$this->db->bind(':nickname', $data['nickname']);
		$this->db->bind(':balance', 0);
		// hashed
		$this->db->bind(':password', $data['password']);

		// make query 
		if ($this->db->execute()) {
			return true;
		} else {
			return false;
		}
	}

	////////////////////////////////////////////////////
	// Checks in the database for the nickname and notHashedPass
	// tries to verify password
	// return row or false 
	public function login($nickname, $notHashedPass)
	{
		// get the row whith given nickname 
		$this->db->query("SELECT * FROM users WHERE `nickname` = :nickname");

		$this->db->bind(':nickname', $nickname);

		$row = $this->db->singleRow();

		if ($row) {
			$hashedPassword = $row->password;
		} else {
			return false;
		}

		// check password
		if (password_verify($notHashedPass, $hashedPassword)) {
			return $row;
		} else {
			return false;
		}
	}

	//////////////////////////////////////////////////
	// paruosiama sql uzklausa
	// UPDATE - pakeista komanda DB nurodanti PAKEISTI tam tikras eiluciu ir stulpeliu reiksmes
	public function updateBalance($nickname, $newBalance) {
		$this->db->query("UPDATE users SET balance = :balance WHERE `nickname` = :nickname");
		// uzklausos kintamuju istatymas
		$this->db->bind(':nickname', $nickname);
		$this->db->bind(':balance', $newBalance);
		// uzklausos vykdymas
		return $this->db->execute();
	}
}
