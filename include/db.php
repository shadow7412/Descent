<?php
/*
Database options are in config/settings.php - use this to change such options.
db->query([query])   // Runs a query, returns the result. Shows a traceback if query fails (does not kill php).
db->get()            // Returns the next result from the list or FALSE. Usage: 'while($result = $db->get());'
db->getObj()         // As above, but a generic object instead of an array.
db->numRows()        // Returns the amount of results the query yielded.
db->commit()         // Commit any changes made to the database.
db->oops()           // Cancel any pending changes to the database.

IMPORTANT: If you do not commit changes, they will not be made.
*/

class db {
	static public $database;
	static private $link = null;
	static private $connections = 0;
	private $lastRequest;

	function __construct(){
		
		//If the database class is already instantitated, use the current connection instead of making another.
		if(self::$connections++==1 && !$override) return false;
		//As the database may be called from either a page or the index, cover both scenarios.
		if(file_exists("../config/settings.php"))
			require "../config/settings.php";
		else if(file_exists("config/settings.php"))
			require "config/settings.php";
		else 
			die("Settings file not found. Please see documentation.");
		self::$link = mysql_connect("localhost",$settings["db_user"],$settings["db_pwd"]);
		if(!self::$link) die("Could not connect to sql server.");
		mysql_select_db($settings["db_database"]) or die("What database?");
		$this->query("SET AUTOCOMMIT=0");
		$this->query("BEGIN");
	}
	
	function __destruct(){
		if(--self::$connections==0){ //only destroy the database connection as the last db class to unload
			$this->query("ROLLBACK"); //rollback any uncommitted changes
			$this->query("SET AUTOCOMMIT=1"); //return autocommit to default
			mysql_close(self::$link);
		}
	}

	public function query($query){
		if($result = mysql_query($query)){
			return $this->lastRequest = $result;
		} else {
			trigger_error(mysql_error());
			$this->query("ROLLBACK");
		}
	}
	
	public function get($request = null){
		if(isset($request))
			return mysql_fetch_assoc($request);
		else
			return mysql_fetch_assoc($this->lastRequest);
	}
	
	public function getObj($request = null){
		if(isset($request))
			return mysql_fetch_object($request);
		else
			return mysql_fetch_object($this->lastRequest);
	}
	
	public function numRows($request = null){
		if(isset($request))
			return mysql_num_rows($request);
		else
			return mysql_num_rows($this->lastRequest);
	}
	
	//If one does not run this command, changes to the database WILL NOT HAPPEN.
	public function commit(){
		$this->query("COMMIT");
	}
	
	public function oops(){
		$this->query("ROLLBACK");
	}
}
