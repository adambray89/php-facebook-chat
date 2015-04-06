<?php

	if(!defined('SITE')) {
		die('<h1>Invalid file access</h1>');	
	}
	
	class Validation {
		
		// Validation status
		private $_passed = false;
		
		// Validation errors
		private $_errors = array();
		
		
		public function check(array $items)
		{
			foreach($items as $element => $rules_array) {
				
				$field_name 		= (isset($rules_array['field_name'])) ? $rules_array['field_name'] : $element; 
				$value 				= $rules_array['value'];
				
				foreach($rules_array as $rule => $rule_value) {
					if($rule === 'required' && empty($value)) {
						$this->addError( $field_name . ' is required' );
					}
					elseif( !empty( $value ) ) {
						switch( $rule ) {	
							case 'max':
								if( strlen( $value ) > $rule_value ) {
									$this->addError( $field_name . ' must be a maximum of ' . $rule_value . ' characters' );	
								}
							break;
							
							case 'min':
								if( strlen( $value ) < $rule_value ) {
									$this->addError( $field_name . ' must be a minimum of ' . $rule_value . ' characters' );	
								}
							break;
						}
					}	
				}
			}
			
			if(empty($this->_errors)){
				$this->_passed = true; 
			}
			 
			return $this;
		}
		 

		private function addError( $error )
		{
			$this->_errors[] = $error; 
		}
		
		
		public function errors()
		{
			return $this->_errors; 
		}
		
		
		public function passed()
		{
			return $this->_passed; 
		}
		
	}

?>