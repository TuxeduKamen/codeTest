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
			    		//alert("callAPI.php?" + theStringPass);
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
							                //document.getElementById("gridResult").innerHTML = this.responseText;
									//alert("Result: " + this.responseText);
									eval(this.responseText);
							        }
						        };
						        xmlhttp.open("POST", "callAPI.php?" + theStringPass, true);
						        xmlhttp.send();
						}
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

					$sql = "SELECT * FROM addressList ORDER BY ID ASC";
					$result = $conn->query($sql);

					if ($result->num_rows > 0)
						{
 			   			// output data of each row
							while($row = $result->fetch_assoc()) 
								{
							        	echo "<tr>";
									echo "<td>" . $row["id"]. "</td><td>" . $row["streetAddress"]. "</td><td>" . $row["cityAddress"]. "</td><td>" . $row["stateAddress"]. "</td><td>" . $row["zipAddress"]. "</td><td>" . $row["dpv_match_code"]. "</td><td>" . $row["validation_source"]. "</td>";
					    				echo "</tr>";
								}
						}
					else 
						{
							    //echo "0 results"; do nothing
						}	
					$conn->close();
				?>
				</tbody>
			</table>
		</div>
	</body>
	
</html>