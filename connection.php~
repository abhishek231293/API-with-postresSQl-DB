<?php
class Connection {
	protected $serverName = "10.25.121.245";
	protected $databaseName = "kerala";
	protected $username = "postgres";
	protected $databasePassword = 'postgres';
	public function getConnection(){
		try {
			$connection =$this->connection = new PDO("pgsql:host=$this->serverName;dbname=$this->databaseName", $this->username, $this->databasePassword);
			// set the PDO error mode to exception
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $connection;
		}catch (PDOException $e){
			echo "Error: " . $e->getMessage();  die(' KOKOOKOK ');
		}
	}
}
?>
