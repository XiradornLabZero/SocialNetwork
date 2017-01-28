<?php
/**
 * Class DB for Database Connection and DB function
 * for a db access and manage purpose
 * @author Sir Xiradorn <[xiradorn@gmail.com]>
 * @version 1.0.0
 */

class DB {

	/**
	 * Connect to DB with PDO classes approach
	 * @return PDO
	 */
	private static function connect() {
		
		$pdo = new PDO('mysql:host=127.0.0.1;dbname=socialnetwork;charset=utf8', 'root', '');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	
	}

	/**
	 * Query function for menage DB
	 * @param  string $query  first part of the query string
	 * @param  array  $params parameters executed and parsed
	 * @return array          
	 */
	public static function query($query, $params = array()) {
		
		$statement = self::connect()->prepare($query);
		$statement->execute($params);

		if (explode(' ', $query)[0] == 'SELECT') {
			$data = $statement->fetchAll();
			return $data;
		}

	}

}