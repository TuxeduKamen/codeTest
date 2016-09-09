<?php

	// get the parameter from URL
	$streetAddress = $_REQUEST["theStreet"];
	$cityAddress = $_REQUEST["theCity"];
	$stateAddress = $_REQUEST["theState"];
	$id = "NEW";
	$zipAddress = "";
	$validFlag = 1;
	$errorDesc = "";
	$validationSource = "";
	date_default_timezone_set("America/Los_Angeles");

	//check against API
	
	//if valid
	if ((strlen(trim($streetAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "\\n wrong street address";
		}
	if ((strlen(trim($cityAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "\\n wrong city address";
		}
	if ((strlen(trim($stateAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "\\n wrong state address";
		}
	
	if ($validFlag == 1) //check SmartysStreet
		{

			$authId = urlencode("cc42450d-7f15-a49a-8075-a5b19a8db50a");
			$authToken = urlencode("JCutMMLdfzVVmhrs9EiB");
			$rawStreet = urlencode($streetAddress); 
			$rawCity = urlencode($cityAddress);
			$rawState = urlencode($stateAddress);
			$apiRequest = "https://api.smartystreets.com/street-address/?street={$rawStreet}&city={$rawCity}&state={$rawState}&auth-id={$authId}&auth-token={$authToken}";
			$apiResponseString = file_get_contents($apiRequest);
			$apiResponse = json_decode($apiResponseString, true);
			$cacheFile = 'cache' . DIRECTORY_SEPARATOR . md5($apiRequest); // transpose url into string and make it the cache filename ex. cache/abcdef123456...

			if (file_exists($cacheFile)) // yes cache
				{
        				$cacheTime = filectime($cacheFile);

        				// return cached data
        				if ($cacheTime > strtotime('-60 minutes')) 
						{
            						$apiResponse = json_decode(file_get_contents($cacheFile),true);
							//check if response is valid
							if(isset($apiResponse[0])) 
								{
									$zipAddress = $apiResponse[0]['components']['zipcode'];
									$validatedUSPS = $apiResponse[0]['analysis']['dpv_match_code'];
									$validationSource = "cache";
									$validFlag = 1;
								}
							else
								{
									$validFlag = 0;
									$errorDesc = $errorDesc . "\\n cached SmartysStreets did not verify address";
								}
        					}
					else
						{
				        		// delete cache file if old
		        				unlink($cacheFile);
						}
    				}
			else // no cache
				{		
					//check if response is valid
					if(isset($apiResponse[0])) 
						{
							$zipAddress = $apiResponse[0]['components']['zipcode'];
							$validatedUSPS = $apiResponse[0]['analysis']['dpv_match_code'];
							$validationSource = "API";
						}
					else
						{
							$validFlag = 0;
							$errorDesc = $errorDesc . "\\n SmartysStreets did not verify address";
						}
					
					//create cache file for this request
    					$fh = fopen($cacheFile, 'w');
		    			fwrite($fh, $apiResponseString);
		    			fclose($fh);
				}
		}

	if ($validFlag == 1) //add to database
		{
			//insert to database
			$servername = "localhost";
			$username = "root";
			$password = "123Caputdracunis!";
			$dbname = "codetest";

			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) 
				{
			    		die("Connection failed: " . $conn->connect_error);
					//$errorDesc = $errorDesc . "\\n Connection failed";
				} 

			$sql = "INSERT INTO addressList (streetAddress, cityAddress, stateAddress, zipAddress, dpv_match_code, validation_source) VALUES (\"" . $streetAddress . "\",\"" . $cityAddress . "\",\"" . $stateAddress . "\",\"" . $zipAddress . "\",\"" . $validatedUSPS ."\",\"" . $validationSource . "\")";

			if ($conn->query($sql) === TRUE) 
				{
					//echo "Saved!";
				} 
			else 
				{
			    		echo "Error: " . $sql . "<br>" . $conn->error;
					//$errorDesc = $errorDesc . "\\n Error in SQL";
				}
			$conn->close();

			//append to table
			echo "var table = document.getElementById(\"tableList\");";
			echo "var row = table.insertRow(-1);";
			echo "var cell1 = row.insertCell(0);";
			echo "var cell2 = row.insertCell(1);";
			echo "var cell3 = row.insertCell(2);";
			echo "var cell4 = row.insertCell(3);";
			echo "var cell5 = row.insertCell(4);";
			echo "var cell6 = row.insertCell(5);";
			echo "var cell7 = row.insertCell(6);";
			echo "cell1.innerHTML = \"" .$id. "\";";
			echo "cell2.innerHTML = \"" .$streetAddress. "\";";	
			echo "cell3.innerHTML = \"" .$cityAddress. "\";";	
			echo "cell4.innerHTML = \"" .$stateAddress. "\";";	
			echo "cell5.innerHTML = \"" .$zipAddress. "\";";	
			echo "cell6.innerHTML = \"" .$validatedUSPS. "\";";	
			echo "cell7.innerHTML = \"" .$validationSource. "\";";	

			//echo "alert(\"" . $apiResponse . "\");";	
			//echo "alert(\"Saved!\");";	
		
		}
	else
		{
			//not valid
			echo "alert(\"An error has occured:" . $errorDesc . "\");";	
		}

?>