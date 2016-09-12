<?php
	class anAddress
		{
			//properties
			private $rawStreet=""; 
			private $rawCity="";
			private $rawState="";
			private $rawZip="";
			
			public function set_rawStreet($new_rawStreet) 
				{
					$this->rawStreet = $new_rawStreet;
				}
			public function get_rawStreet() 
				{
					return $this->rawStreet;
				}
			public function set_rawCity($new_rawCity) 
				{
					$this->rawCity = $new_rawCity;
				}
			public function get_rawCity() 
				{
					return $this->rawCity;
				}
			public function set_rawState($new_rawState) 
				{
					$this->rawState = $new_rawState;
				}
			public function get_rawState() 
				{
					return $this->rawState;
				}
			public function set_rawZip($new_rawZip) 
				{
					$this->rawZip = $new_rawZip;
				}
			public function get_rawZip() 
				{
					return $this->rawZip;
				}
			
			//utility methods..

		}
?>