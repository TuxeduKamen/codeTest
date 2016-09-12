<?php
	require_once 'anAddress.php';
	class mrSmartysChecker extends anAddress
		{
			private $validFlag = 1;
			private $errorDesc = "";
			private $authId;
			private $authToken;
			private $apiRequest="";
			private $apiResponseString=""; //this is written to cache file
			private $apiResponse="";
			private $cacheFile=""; // transpose url into string and make it the cache filename ex. cache/abcdef123456...
			private $validatedUSPS="";
			private $validationSource="";

			public function get_validFlag() 
				{
					return $this->validFlag;
				}
			public function set_validFlag($new_validFlag) 
				{
					$this->validFlag = $new_validFlag;
				}
			public function set_authId($new_authId) 
				{
					$this->authId = $new_authId;
				}
			public function get_authId() 
				{
					return $this->authId;
				}
			public function set_authToken($new_authToken) 
				{
					$this->authToken = $new_authToken;
				}
			public function get_authToken() 
				{
					return $this->authToken;
				}
			public function set_apiRequest() 
				{	
					$this->apiRequest = "https://api.smartystreets.com/street-address/?street=" . urlencode($this->get_rawStreet()) . "&city=" . urlencode($this->get_rawCity()) . "&state=" . urlencode($this->get_rawState()) . "&auth-id={$this->get_authId()}&auth-token={$this->get_authToken()}";
				}
			public function get_apiRequest() 
				{
					return $this->apiRequest;
				}
			public function set_cacheFile($new_cacheFile) 
				{
					$this->cacheFile = $new_cacheFile;
				}
			public function get_cacheFile() 
				{
					return $this->cacheFile;
				}
			public function get_apiResponse() 
				{
					return $this->apiResponse;
				}	
			public function set_apiResponse($new_apiResponse) 
				{
					$this->apiResponse = $new_apiResponse;
				}
			public function set_errorDesc($new_errorDesc) 
				{
					$this->errorDesc = $new_errorDesc;
				}
			public function get_errorDesc() 
				{
					return $this->errorDesc;
				}	
			public function set_validatedUSPS($new_validatedUSPS) 
				{
					$this->validatedUSPS = $new_validatedUSPS;
				}
			public function get_validatedUSPS() 
				{
					return $this->validatedUSPS;
				}
			public function set_validationSource($new_validationSource) 
				{
					$this->validationSource = $new_validationSource;
				}
			public function get_validationSource() 
				{
					return $this->validationSource;
				}
			/*
			public function set_rawZip($new_rawZip) //override anAddress
				{
					$this->rawZip = $new_rawZip;
				}
			*/

			public function validate()
				{
					$this->set_cacheFile("cache" . DIRECTORY_SEPARATOR . md5($this->get_apiRequest()));
					if (file_exists($this->get_cacheFile())) // yes cache
						{
        						$cacheTime = filectime($this->get_cacheFile());
		        				// return cached data
        						if ($cacheTime > strtotime('-60 minutes')) 
								{
            								$this->set_apiResponse(json_decode(file_get_contents($this->get_cacheFile()),true));
									//check if response is valid
									if(isset($this->get_apiResponse()[0])) 
										{
											$this->set_rawZip($this->get_apiResponse()[0]['components']['zipcode']);
											$this->set_validatedUSPS($this->get_apiResponse()[0]['analysis']['dpv_match_code']);
											$this->set_validationSource("cache");
											$this->set_validFlag(1);
										}
									else
										{
											$this->set_validFlag(0);
											$this->set_errorDesc($this->get_errorDesc() . "cached SmartysStreets did not verify address");
										}
        							}
							else
								{
				        				// delete cache file if old
		   			     				unlink($this->get_cacheFile());
								}
    						}
					else // no cache
						{		
							$this->set_apiRequest();
							$apiResponseString = file_get_contents($this->get_apiRequest());
							$this->set_apiResponse(json_decode($apiResponseString,true));
							//check if response is valid
							if(isset($this->get_apiResponse()[0])) 
								{
									$this->set_rawZip($this->get_apiResponse()[0]['components']['zipcode']);
									$this->set_validatedUSPS($this->get_apiResponse()[0]['analysis']['dpv_match_code']);
									$this->set_validationSource("API");
									$this->set_validFlag(1);
								}
							else
								{
									$this->set_validFlag(0);
									$this->set_errorDesc($this->get_errorDesc() . "SmartysStreets did not verify address");
								}
					
							//create cache file for this request
    							$fh = fopen($this->get_cacheFile(), 'w');
		    					fwrite($fh, $apiResponseString);
				    			fclose($fh);
						}

				}
		}
?>