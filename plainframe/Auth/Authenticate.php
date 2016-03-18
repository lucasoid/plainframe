<?php
namespace plainframe\Auth;

/**
 * Class for managing user authentication.
 * 
 * The class uses PHPass to implement key stretching, password hashing, and salting.
 * @param	string	$userid
 * @param 	string 	$password	The user's unhashed password to check
 * @return 	boolean
 */


class Authenticate {

	/**
	 * method to authenticate a user.
	 * @param string $userid
	 * @param string $password
	 * @param object \Data\SqlDB
	 * @return boolean
	 */
	public static function authenticate($userid, $password, $conn) {
		
		$qry = "SELECT userid, hashed_pass FROM users WHERE userid=?";
		$stmt = $conn->prepare($qry);
		$stmt->execute(array($userid));
		$row = $stmt->fetch();
		if($row) {
			$storedpassword = $row['hashed_pass'];
			$active = 1;
			$hasher = new PasswordHash(8, false);
			if($hasher->checkPassword($password, $storedpassword) && $active == 1) {
				return true;
			}
			else {
				return false;
			}
		}
		return false;
	}
	/**
	 * method to create a hashed password.
	 * @param string $entered_password
	 * @return string
	 */
	public static function createHashedPassword($entered_password) {
		$hasher = new PasswordHash(8, false);
		$hashedpass = $hasher->HashPassword($entered_password);
		return $hashedpass;
	}
	
	
}

?>