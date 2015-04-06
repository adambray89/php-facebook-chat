<?php
	if(!defined('SITE')) {
		die('<h1>Invalid file access</h1>');	
	}
	
	class Database {
		
		// Stored connection
		private $_connection = null;
		
		// Database config
		protected $dbconfig = array();
		
		// Query results
		private $_results_object;
		private $_count;
		
		// Query error flag
		private $_error = false;
		private $_error_message = array();
		
		
		public function __construct(array $db_info)
		{	
			if( !isset( $this->dbconfig['dsn'] ) ) {
				$this->dbconfig['dsn'] 				= 'mysql:host=' . $db_info['host'] .'; dbname='. $db_info['db'] .'; charset=utf8';
				$this->dbconfig['username']			= $db_info['user'];
				$this->dbconfig['password']			= $db_info['pass'];
				$this->dbconfig['driverOptions']	= $db_info['options'];
			}
		}
	
	
		public function connect()
		{
			if($this->_connection) {
				return;
			}
	  
			try {
				$this->_connection = new PDO( 
				$this->dbconfig['dsn'],
                $this->dbconfig['username'],
                $this->dbconfig['password'],
                $this->dbconfig['driverOptions']);
					
				$this->_connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$this->_connection->setAttribute( PDO::ATTR_EMULATE_PREPARES, false ); 
			}
			catch (PDOException $e) {
				throw new RunTimeException($e->getMessage());
			}
		}
		
		public function query($query, $data = array())
		{		
			$this->connect();
			
			$this->_query = $this->_connection->prepare($query);
			
			if($this->_query->execute($data)) {	
				$this->_count = $this->_query->rowCount();
				$this->_results_object = $this->_query->fetchAll(PDO::FETCH_OBJ);
			}
			else {
				throw new RuntimeException('There has been a problem executing the following query: ' . $query);
			}
			return true;
		}
		
		
		public function fetch()
		{
			return $this->_results_object;	
		}
		
		
		public function count()
		{
			return $this->_count;	
		}
	}
?>