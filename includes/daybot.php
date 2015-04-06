<?php

	class Daybot {
		
		// Daybot commands
		private $_commands = array(
			'!follow'		=>			'You can follow Adam on Twitter - @AdamBrayWeb',	
		);
		
		private $_convos = array(
			'hello'					=>			'Hey, how\'s it going? I\'m your friendly Daybot =]',
			' hi '					=>			'Hey, how\'s it going? I\'m your friendly Daybot =]',
			'hey'					=>			'I just met you, and this is crazy, here\'s my number.. so call me maybe?',
			'test'					=>			'Jees, another test!? Don\'t you guys realise this works already.',
			'how are you'			=>			'I\'m good, just crunching some numbers .. well, 1\'s and 0\'s',
			'you?'					=>			'I\'m good, just crunching some numbers .. well, 1\'s and 0\'s',
			'cool'					=>			'Damn it\'s hot in hur',
			'sdf'					=>			'...',
			'whats up'				=>			'Just watching the game, having a bud',
			'sup'					=>			'Just watching the game, having a bud',
			'what\'s up'			=>			'Just watching the game, having a bud',
			'haha'					=>			'thank you, thank you .. I\'m here till Tuesday!',
			'lol'					=>			'ROFLCOPTER .... woooOOOOOOSH!!!1!',
		);
		
		// Bot action required flag
		private $_action = false;
		
		// Stored response
		private $_response = null;
		
		
		public function checkCommands($message)
		{
			$message = strtolower($message);
			
			foreach(explode(' ',$message) as $word) {
				if(array_key_exists($word,$this->_commands)) {
					$this->_action = true;
					$this->_response = $this->_commands[$word];
					
					return $this;	
				}
			}
			
			foreach($this->_convos as $msg => $response) {
				if(strpos($message,$msg) !== false) {
					$this->_action = true;
					$this->_response = $response;
					
					return $this;	
				}
			}
			
			return $this;
		}
		
		public function getResponse()
		{	
			return array(
				'ip'		=>		'1.1.1.1',
				'message'	=>		$this->_response,
				'name'		=>		'Daybot',
				'time'		=>		time(),
			);	
		}
		
		public function addResponse($response)
		{
			if(!isset($this->_response)) {
				$this->_response = $response;
			}
			return $this;
		}
		
		public function actionRequired()
		{
			return $this->_action;	
		}
			
	}

?>