<?php

	//---------------------------------------------------------------------A. Declare and initialize global variables for this page
	//---------------------------------------------------------------------A.1 Fetch the values from the calling page
	$streetAddress = $_REQUEST["theStreet"];
	$cityAddress = $_REQUEST["theCity"];
	$stateAddress = $_REQUEST["theState"];
	
	//---------------------------------------------------------------------A.2 Declare other variables
	date_default_timezone_set("America/Los_Angeles");
	$id = "NEW";
	$zipAddress = "10000";
	$validFlag = 1;
	$errorDesc = "";
	$validationSource = "none";
	$validatedUSPS = "N";
								
	//---------------------------------------------------------------------B check against API
	//---------------------------------------------------------------------B.1 Check if address is valid (move error checking role to calling page? or this page?)
	if ((strlen(trim($streetAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "wrong street address.";
		}
	if ((strlen(trim($cityAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "wrong city address.";
		}
	if ((strlen(trim($stateAddress)))>0)
		{
			$validFlag = 1;	
		}
	else
		{
			$validFlag = 0;
			$errorDesc = $errorDesc . "wrong state address.";
		}
	
	//---------------------------------------------------------------------B.2 Submit to SmartysStreet if address seems ok
	if ($validFlag == 1) 
		{
			require_once 'mrSmartysChecker.php';
			$mrAPI = new mrSmartysChecker(); //create an object Mr. API
			$mrAPI->set_rawStreet($streetAddress);
			$mrAPI->set_rawCity($cityAddress);
			$mrAPI->set_rawState($stateAddress);
			$authId = urlencode("cc42450d-7f15-a49a-8075-a5b19a8db50a");
			$authToken = urlencode("JCutMMLdfzVVmhrs9EiB");
			$mrAPI->set_authId($authId);
			$mrAPI->set_authToken($authToken);
			$mrAPI->set_apiRequest();
			$mrAPI->validate();
			$apiResponse = $mrAPI->get_apiResponse();
			$validFlag = $mrAPI->get_validFlag();
			if ($validFlag==0)
				{
					$errorDesc = $errorDesc . $mrAPI->get_errorDesc();
				}
		}
	//---------------------------------------------------------------------C Process Good Address
	if ($validFlag == 1)
		{	
			$streetAddress = $mrAPI->get_rawStreet();
			$cityAddress = $mrAPI->get_rawcity();
			$stateAddress = $mrAPI->get_rawstate();
			$zipAddress = $mrAPI->get_rawZip();
			$validatedUSPS = $mrAPI->get_validatedUSPS();
			$validationSource = $mrAPI->get_validationSource();
	//---------------------------------------------------------------------C.1 Add to database
			try
				{
					$db = new PDO('mysql:host=localhost;dbname=codeTest;charset=utf8mb4', 'codeTest', 'codeTest123');
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					
					// bind parameter fields to insert
					$stmt = $db->prepare("INSERT INTO addressList (streetAddress, cityAddress, stateAddress, zipAddress ,dpv_match_code ,validation_source) VALUES (:streetAddress, :cityAddress, :stateAddress, :zipAddress, :dpv_match_code, :validation_source)");
    					$stmt->bindParam(':streetAddress', $streetAddressR);
    					$stmt->bindParam(':cityAddress', $cityAddressR);
    					$stmt->bindParam(':stateAddress', $stateAddressR);
					$stmt->bindParam(':zipAddress', $zipAddressR);
					$stmt->bindParam(':dpv_match_code', $dpv_match_codeR);
					$stmt->bindParam(':validation_source', $validation_sourceR);

    					$streetAddressR=$streetAddress;
    					$cityAddressR=$cityAddress;
    					$stateAddressR=$stateAddress;
					$zipAddressR=$zipAddress;
					$dpv_match_codeR=$validatedUSPS;
					$validation_sourceR=$validationSource;
    					$stmt->execute();

				}
			catch (PDOException $errDesc)
				{
					//$db->rollBack();
			    		echo $errDesc->getMessage();
				}


	//---------------------------------------------------------------------C.2 Add to grid
	        	echo json_encode(array('theID' => $id,'streetAddress'=> $mrAPI->get_rawStreet(),'cityAddress'=> $mrAPI->get_rawcity(),'stateAddress'=> $mrAPI->get_rawstate(),'zipAddress'=> $mrAPI->get_rawZip(),'validatedUSPS'=> $mrAPI->get_validatedUSPS(),'validationSource'=> $mrAPI->get_validationSource()));
        	
		}
	else
		{
			//not valid
			echo "An error has occured:" . $errorDesc;	
		}

?>