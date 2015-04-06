<?php
	if(!defined('SITE')) {
		die('<h1>Invalid file access</h1>');	
	}

	class Chat {
		
		// Database dependency
		protected $_db = null;
		
		// Token dependency
		protected $_token = null;
		
		// Session dependency
		protected $_session = null;
		
		// Bot dependency
		protected $_bot = null;
		
		// Validation instance
		private $_validation = null;
		
		
		public function __construct(Database $db, Token $token, Session $session)
		{
			if(!isset($this->_db)) {
				$this->_db = $db;	
			}
			
			if(!isset($this->_token)) {
				$this->_token = $token;	
			}
			
			if(!isset($this->_session)) {
				$this->_session = $session;	
			}
		}
		
		public function getPosts()
		{	
			$query_data = array(
				'two_weeks'		=>		strtotime('-2 weeks'),
			);
			
			$this->_db->query('SELECT chat.chat_id 
			FROM chat
			WHERE chat.delete = 0
			AND chat.time > :two_weeks', $query_data);
			
			$total_messages = $this->_db->count();
			
			if( $total_messages > 0 ) {
				
				$query_data['offset']	= ($total_messages - 50);
				$query_data['limit']	= $total_messages;
			
				$this->_db->query('SELECT chat.chat_id
				, chat.name
				, chat.message
				, chat.time 
				, chat.ip
				FROM chat
				WHERE chat.delete = 0
				AND chat.time > :two_weeks
				LIMIT :offset, :limit;', $query_data);
			
				$i = 1;
				$posts = '';
				$previous_post_time = time();
				
				foreach($this->_db->fetch() as $row) {
					if((($row->time - $previous_post_time) > 3600) || $i === 1) {
						$posts .= '<li class="time">'.date('l jS F G:i', $row->time).'</li>';	
					}
					
					// BOT ADDON
					$row->name = ($row->ip === '1.1.1.1') ? '<span style="color: red;">'.$row->name.'</span>' : $row->name;
					// END BOT ADDON
					
					$posts .= $this->formatMessage($row->name,$row->message);
					
					$i += 1;
					$previous_post_time = $row->time;	
				}
				
				return $posts;
			}
			else {
				throw new RuntimeException('There are currently no messages to be displayed');	
			}
		}
		
		public function addMessage($name, $message, $token)
		{
			if($this->_token->check($token)) {
				
				$anti_spam = $this->_session->get('anti_spam');
				
				if($anti_spam <= time()) {
				
					$message 	= $this->clean($message);
					$name		= $this->clean($name);
					
					$this->_validation = new Validation();
					
					if($this->_validation->check(array(
						'chat_message'		=>		array(
							'max'				=>		140,
							'min'				=>		2,
							'required'			=>		true,
							'value'				=>		$message,	
						),
						'chat_name'			=>		array(
							'max'				=>		15,
							'min'				=>		2,
							'required'			=>		true,
							'value'				=>		$name,
						)
					))->passed()) {
						
						$query_data = array(
							'ip'		=>		IP,
							'message'	=>		$message,
							'name'		=>		$name,
							'time'		=>		time(),
						);
						
						// EXTENDED BOT ACTIONED
						if(!isset($this->_bot)) {
							$this->_bot = new Daybot();	
						}
						
						if(stripos($message,'http://') !== false || stripos($message,'www.') !== false || stripos($message,'.com') !== false) {
							$this->_db->query('INSERT INTO chat (name, message, time, ip) VALUES (:name, :message, :time, :ip);', 
							$this->_bot->addResponse('Sorry ' . $name . ', links aren\'t allowed!')->getResponse());
						} // END BOT ACTIONS #1
						else {
							$this->_db->query('INSERT INTO chat (name, message, time, ip) VALUES (:name, :message, :time, :ip);', $query_data);
	
							$this->_session->put('anti_spam',strtotime('+5 seconds'));
							
							// BOT COMMANDS
							if($this->_bot->checkCommands($message)->actionRequired()) {
								$this->_db->query('INSERT INTO chat (name, message, time, ip) VALUES (:name, :message, :time, :ip);',$this->_bot->getResponse());
							}
							// END BOT COMMANDS
							
							return '<li class="time">You</li>' . $this->formatMessage($name,$message);
						}
					}
					else {
						throw new RuntimeException(implode(', ', $this->_validation->errors()));		
					}
				}
				else {
					throw new RuntimeException('You cannot post multiple messages within 5 seconds of eachother');	
				}
			}
			else {
				throw new RuntimeException('Invalid post token used when adding a message');
			}
		}
		
		private function formatMessage($name, $message)
		{
			$message = wordwrap($message, 22, ' ', true);
			return '<li><strong>'.$name.':</strong> '.$message.'</li>';	
		}
		
		private function clean($string)
		{
			return htmlspecialchars(strip_tags($string), ENT_QUOTES,'UTF-8',false);	
		}
	}
?>