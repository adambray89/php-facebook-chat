<?php
	
	if(!defined('SITE')) {
		die('<h1>Invalid file access</h1>');	
	}
	
	class Token {
		
		// Session dependency
		protected $_session = null;
		
		// Store the security token for forms
		private $_token = array();
		
		// Key for active token
		private $_active_token_key;
		
		
		public function __construct(Session $session)
		{
			if(!isset($this->_session)) {
				$this->_session = $session;
			}
		}
		
		public function set()
		{
			$this->_token = md5(uniqid('', true));
			return $this->_session->put( '_token', $this->_token );
		}
		
		public function check( $token )
		{
			if( $this->_session->exists( '_token' ) && $token == $this->_session->get( '_token' ) ) {
				return true;	
			}
		}
	}

?>