<?php

	if(!defined('SITE')) {
		die('<h1>Invalid file access</h1>');	
	}

	class Session {
		
		public function put( $name, $value )
		{
			return $_SESSION[$name] = $value;
		}
		
		
		public function exists( $name )
		{
			return ( isset($_SESSION[$name])) ? true : false;
		}
		
		
		public function get( $name )
		{
			return $_SESSION[$name];
		}
		

		public function destory( $name )
		{
			if( $this->exists($name)) {
				unset( $_SESSION[$name] );	
			}
			
			return true;
		}
		
	}

?>