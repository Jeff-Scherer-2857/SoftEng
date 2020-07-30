<?php 
require_once __DIR__ . '/Node.php';

// SINGLETON - there should only be ONE elevator supervisory controller!
class SuperNode extends Node {
	// class variables	
	private static $instance = null;
	private static $DBConnection = null;
		
	
	// class methods
	private static function ConnectDB() {
		try {
			static::$DBConnection = new PDO(
				'mysql:host=127.0.0.1;port=3306;dbname=elevator',     //Data source name 
				'webuser',                                                 //Username
				'12345678'                                                      //Password
			);
			static::$DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			echo "connection successful<br>";
		} catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage() . "<br>";
		}
	}
	
	public function __construct($ID) {
		$this->CANID = $ID;
	}
		
	public static function Instance($ID) {
				
		// SINGLETON!
		if (static::$instance == null) {
			static::$instance = new SuperNode($ID);
			//parent::$CANID = $ID;
			
			static::ConnectDB();
		} else {
			echo "There is already an instance of SuperNode!<br>";
		}
		
		return static::$instance;
	}
	
	public function GetDBConnector() {
		return static::$DBConnection;
	}
	
	public function QueryDatabase($query, $params) {
		
		$statement = self::$DBConnection->prepare($query);
		
		$result = $statement->execute($params);
		$result = $statement->fetchAll();
		
		return $result;
	}
	
	public function PlayAudioFile($audioFile) {
		// dummy code to play audio file on the Pi
	}
}

?>