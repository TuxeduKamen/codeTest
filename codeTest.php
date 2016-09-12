<html>
	<head>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script>
			function throwAddress() 
				{
					var theStreet = document.getElementById("streetAddress").value;
					var theCity = document.getElementById("cityAddress").value;
					var theState = document.getElementById("stateAddress").value;
					theStreet = theStreet.replace(/[^a-zA-Z0-9 ]/g, "");
					theCity = theCity.replace(/[^a-zA-Z0-9 ]/g, "");
					theState = theState.replace(/[^a-zA-Z0-9 ]/g, "");
					var theStringPass = "theStreet=" + theStreet + "&theCity=" + theCity + "&theState=" + theState;
			    		if (theStringPass.length == 0) 
						{ 
						        document.getElementById("gridResult").innerHTML = "Empty";
						        return;
						} 
					else 
						{
						        var xmlhttp = new XMLHttpRequest();
						        xmlhttp.onreadystatechange = function() 
							{
						            if (this.readyState == 4 && this.status == 200) 
								{
									try
										{
        										var theResult=JSON.parse(this.responseText);
											if (theResult.hasOwnProperty('theID'))
												{
													if (theResult.theID == "NEW")
														{
															appendToGrid(theResult);
														}
													else
														{
															alert("There was an error with the page call\nPlease try again.");
														}
												}
											else
												{
													alert("Result was not in readable format\nPlease try again");
												}
										}
									catch(e)
										{
        										//alert("error occured");
											//alert(this.responseText + "\n" + e);
											alert(this.responseText); //unParseable
										}
								}
						        };
						        xmlhttp.open("POST", "callAPI.php?", true);
							xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
							xmlhttp.send(theStringPass);
						}
				}

			function appendToGrid(newRecord)
				{
					var table = document.getElementById("tableList");
					var row = table.insertRow(-1);
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					var cell3 = row.insertCell(2);
					var cell4 = row.insertCell(3);
					var cell5 = row.insertCell(4);
					var cell6 = row.insertCell(5);
					var cell7 = row.insertCell(6);
					cell1.innerHTML = newRecord.theID;
					cell2.innerHTML = newRecord.streetAddress;	
					cell3.innerHTML = newRecord.cityAddress;	
					cell4.innerHTML = newRecord.stateAddress;	
					cell5.innerHTML = newRecord.zipAddress;	
					cell6.innerHTML = newRecord.validatedUSPS;	
					cell7.innerHTML = newRecord.validationSource;	
				}

			$(document).ready(function() 
				{
					$('#tableList').DataTable();
				} );
		</script>
	</head>

	<body>
		<p><b><u>Developer Code Test:</u></b></p>
		<p><b>Input Form:</b></p>
		<form> 
			<table>
				<tr>
					<td>Street:</td><td><input id="streetAddress" type="text"></td>
				</tr>
				<tr>
					<td>City:</td><td><input id="cityAddress" type="text"></td>
				</tr>
				<tr>
					<td>State:</td><td><input id="stateAddress" type="text"></td>
				</tr>
				<tr>
					<td></td><td align='right'><input type="button" value="submit" onclick="throwAddress()"></td>
				</tr>
			</table>
		</form>
		<hr/>
		<p><b>Grid List:</b></p>

		<div id="gridResult">
			<table id="tableList" class="cell-border" cellspacing="0" width="100%">
				<thead>
					<tr>
						<td>ID</td><td>Street</td><td>City</td><td>State</td><td>Zip</td><td>Deliverable?</td><td>Validation Source</td>
					</tr>
				</thead>
				<tbody>
				<?php
					//display database contents to grid
					try
						{					
							$db = new PDO('mysql:host=localhost;dbname=codeTest;charset=utf8mb4', 'codeTest', 'codeTest123');
							$stmt = $db->query("SELECT * FROM addressList ORDER BY ID ASC");
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
								{
    									echo "<tr>";
									echo "<td>" . $row["id"]. "</td><td>" . $row["streetAddress"]. "</td><td>" . $row["cityAddress"]. "</td><td>" . $row["stateAddress"]. "</td><td>" . $row["zipAddress"]. "</td><td>" . $row["dpv_match_code"]. "</td><td>" . $row["validation_source"]. "</td>";
						    			echo "</tr>";
								}
						}
					catch (PDOException $errDesc)
						{
							//$db->rollBack();
					    		//echo $errDesc->getMessage();
						}
				?>
				</tbody>
			</table>
		</div>
	</body>
	
</html>