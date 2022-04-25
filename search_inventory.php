<?php

include('lib/common.php');
// written by yzhang3424
/* if form was submitted, then execute query to search for friends */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$typename = mysqli_real_escape_string($db, $_POST['typename']);                                         
	$manufacturername = mysqli_real_escape_string($db, $_POST['manufacturername']);
	$modelyear = mysqli_real_escape_string($db, $_POST['modelyear']);
	$colorname = mysqli_real_escape_string($db, $_POST['colorname']);
	$keyword = mysqli_real_escape_string($db, $_POST['keyword']);
	$vin = mysqli_real_escape_string($db, $_POST['vin']);
	$purchasedvehicle = mysqli_real_escape_string($db, $_POST['purchasedvehicle']);
	$vehiclesoldornot = mysqli_real_escape_string($db, $_POST['vehiclesoldornot']);
//	echo $typename;
//	echo $manufacturername;
//
//	echo $colorname;
//	echo $keyword;
//	echo $vin;
//if (!isset($_SESSION['Username']) || !isset($_SESSION['Role']) || !($_SESSION['Role'] === 'Clerk' || $_SESSION['Role'] === 'Manager' || $_SESSION['Role'] === 'All Roles')) {
//}
	/*
SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, b.totalrepaircost
FROM vehicle INNER JOIN
(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
LEFT JOIN repair ON vehicle.VIN = repair.VIN 
INNER JOIN buy ON vehicle.VIN = buy.VIN
LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN
order by VIN;

SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost FROM vehicle INNER JOIN (SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN LEFT JOIN repair ON vehicle.VIN = repair.VIN INNER JOIN buy ON vehicle.VIN = buy.VIN LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN 
WHERE (vehicle.VIN NOT IN (SELECT VIN FROM sell) AND vehicle.VIN NOT IN (SELECT VIN FROM repair WHERE repair.`Repair Status`= 'pending' OR repair.`Repair Status`= 'in progress')) ORDER BY vehicle.VIN;


*/
if (!isset($_SESSION['Role']) or $_SESSION['Role'] === 'Sales Person') {

	if (!empty($typename) or !empty($manufacturername) or !empty($modelyear) or !empty($colorname) or !empty($keyword) or !empty($vin)) {
		/*$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, relationcolorvehicle.Colors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost` FROM vehicle " . " ".
		"INNER JOIN relationcolorvehicle ON vehicle.VIN = relationcolorvehicle.VIN " .
		"LEFT JOIN repair ON vehicle.VIN = repair.VIN " .
		"INNER JOIN buy ON vehicle.VIN = buy.VIN ";*/

		$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
		FROM vehicle INNER JOIN 
		(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
		LEFT JOIN repair ON vehicle.VIN = repair.VIN 
		INNER JOIN buy ON vehicle.VIN = buy.VIN
		LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN";

		$query = $query . " WHERE (vehicle.VIN NOT IN (SELECT VIN FROM sell) AND vehicle.VIN NOT IN (SELECT VIN FROM repair WHERE repair.`Repair Status`= 'pending' OR repair.`Repair Status`= 'in progress') AND (1=0 OR 1=1 ";

		if (!empty($typename)) {
			$query = $query . " AND vehicle.Types LIKE '%$typename%' ";
		}
		if (!empty($manufacturername)) {
			$query = $query . " AND vehicle.Manufacturer LIKE '%$manufacturername%' ";
		}
		if (!empty($modelyear)) {
			$query = $query . " AND vehicle.Year LIKE '%$modelyear%' ";
//			echo $modelyear;
		}
		if (!empty($colorname)) {
			$query = $query . " AND relationcolorvehicle.Colors LIKE '%$colorname%' ";
		}
		if (!empty($keyword)) {
			$query = $query . " AND (vehicle.Year LIKE '%$keyword%' OR vehicle.Model LIKE '%$keyword%' OR vehicle.Manufacturer LIKE '%$keyword%')";
		}
		if (!empty($vin)) {
			$query = $query . " AND vehicle.VIN LIKE '%$vin%'";
		}
		$query = $query . ")) ";
		$query = $query . " ORDER BY vehicle.VIN";

	}

//	echo $query;


	$result = mysqli_query($db, $query);

	if (mysqli_affected_rows($db) == -1) {
		array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
	}
	if (mysqli_affected_rows($db) == 0) {
		array_push($error_msg,  "Sorry, it looks like we don’t have that in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
	}
	include('lib/show_queries.php');

} elseif ($_SESSION['Role'] === 'Clerk') {
	if(empty($purchasedvehicle)){

		if (!empty($typename) or !empty($manufacturername) or !empty($modelyear) or !empty($colorname) or !empty($keyword) or !empty($vin)) {

			$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
			FROM vehicle INNER JOIN 
			(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
			LEFT JOIN repair ON vehicle.VIN = repair.VIN 
			INNER JOIN buy ON vehicle.VIN = buy.VIN
			LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN";

			$query = $query . " WHERE (vehicle.VIN NOT IN (SELECT VIN FROM sell) AND (1=0 OR 1=1 ";

			if (!empty($typename)) {
				$query = $query . " AND vehicle.Types LIKE '%$typename%' ";
			}
			if (!empty($manufacturername)) {
				$query = $query . " AND vehicle.Manufacturer LIKE '%$manufacturername%' ";
			}
			if (!empty($modelyear)) {
				$query = $query . " AND vehicle.Year LIKE '%$modelyear%' ";
				echo $modelyear;
			}
			if (!empty($colorname)) {
				$query = $query . " AND relationcolorvehicle.Colors LIKE '%$colorname%' ";
			}
			if (!empty($keyword)) {
				$query = $query . " AND (vehicle.Year LIKE '%$keyword%' OR vehicle.Model LIKE '%$keyword%' OR vehicle.Manufacturer LIKE '%$keyword%')";
			}
			if (!empty($vin)) {
				$query = $query . " AND vehicle.VIN LIKE '%$vin%'";
			}
			$query = $query . ")) ";
			$query = $query . " ORDER BY vehicle.VIN";

		}

		echo $query;


		$result = mysqli_query($db, $query);

		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have that in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

	} else {
		$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
		FROM vehicle INNER JOIN 
		(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
		LEFT JOIN repair ON vehicle.VIN = repair.VIN 
		INNER JOIN buy ON vehicle.VIN = buy.VIN
		LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN ORDER BY vehicle.VIN";
		$result = mysqli_query($db, $query);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

		//number of vehicle under repair not in sell
		$query2 = "SELECT COUNT(DISTINCT(VIN)) AS VNISUR FROM (SELECT v.VIN FROM vehicle AS v RIGHT JOIN repair AS r ON r.VIN = v.VIN WHERE (r.`Repair Status` = 'in progress' OR r.`Repair Status` = 'pending') AND v.VIN NOT IN (SELECT VIN FROM sell)) c ";
		$result2 = mysqli_query($db, $query2);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

		//number of vehicle not in sell
		$query3 = "SELECT COUNT(DISTINCT(VIN)) AS VNIS FROM vehicle WHERE vehicle.VIN NOT IN (SELECT VIN FROM sell)";
		$result3 = mysqli_query($db, $query3);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');


	}
} elseif ($_SESSION['Role'] === 'Manager') {
	if (!empty($typename) or !empty($manufacturername) or !empty($modelyear) or !empty($colorname) or !empty($keyword) or !empty($vin)) {

			$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
			FROM vehicle INNER JOIN 
			(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
			LEFT JOIN repair ON vehicle.VIN = repair.VIN 
			INNER JOIN buy ON vehicle.VIN = buy.VIN
			LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN";

			$query = $query . " WHERE (";
			if ($vehiclesoldornot == 'unsold') {
				$query = $query . " vehicle.VIN NOT IN (SELECT VIN FROM sell) AND ";
			}
			if ($vehiclesoldornot == 'sold') {
				$query = $query . " vehicle.VIN IN (SELECT VIN FROM sell) AND ";
			}

			
			$query = $query . " (1=0 OR 1=1";

			if (!empty($typename)) {
				$query = $query . " AND vehicle.Types LIKE '%$typename%' ";
			}
			if (!empty($manufacturername)) {
				$query = $query . " AND vehicle.Manufacturer LIKE '%$manufacturername%' ";
			}
			if (!empty($modelyear)) {
				$query = $query . " AND vehicle.Year LIKE '%$modelyear%' ";
				echo $modelyear;
			}
			if (!empty($colorname)) {
				$query = $query . " AND relationcolorvehicle.Colors LIKE '%$colorname%' ";
			}
			if (!empty($keyword)) {
				$query = $query . " AND (vehicle.Year LIKE '%$keyword%' OR vehicle.Model LIKE '%$keyword%' OR vehicle.Manufacturer LIKE '%$keyword%')";
			}
			if (!empty($vin)) {
				$query = $query . " AND vehicle.VIN LIKE '%$vin%'";
			}
			$query = $query . ")) ";
			$query = $query . " ORDER BY vehicle.VIN";

		}

		echo $query;


		$result = mysqli_query($db, $query);

		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have that in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

} else {
	if(empty($purchasedvehicle)){

		if (!empty($typename) or !empty($manufacturername) or !empty($modelyear) or !empty($colorname) or !empty($keyword) or !empty($vin)) {

			$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
			FROM vehicle INNER JOIN 
			(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
			LEFT JOIN repair ON vehicle.VIN = repair.VIN 
			INNER JOIN buy ON vehicle.VIN = buy.VIN
			LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN";

			$query = $query . " WHERE (";
			if ($vehiclesoldornot == 'unsold') {
				$query = $query . " vehicle.VIN NOT IN (SELECT VIN FROM sell) AND ";
			}
			if ($vehiclesoldornot == 'sold') {
				$query = $query . " vehicle.VIN IN (SELECT VIN FROM sell) AND ";
			}

			
			$query = $query . " (1=0 OR 1=1";

			if (!empty($typename)) {
				$query = $query . " AND vehicle.Types LIKE '%$typename%' ";
			}
			if (!empty($manufacturername)) {
				$query = $query . " AND vehicle.Manufacturer LIKE '%$manufacturername%' ";
			}
			if (!empty($modelyear)) {
				$query = $query . " AND vehicle.Year LIKE '%$modelyear%' ";
				echo $modelyear;
			}
			if (!empty($colorname)) {
				$query = $query . " AND relationcolorvehicle.Colors LIKE '%$colorname%' ";
			}
			if (!empty($keyword)) {
				$query = $query . " AND (vehicle.Year LIKE '%$keyword%' OR vehicle.Model LIKE '%$keyword%' OR vehicle.Manufacturer LIKE '%$keyword%')";
			}
			if (!empty($vin)) {
				$query = $query . " AND vehicle.VIN LIKE '%$vin%'";
			}
			$query = $query . ")) ";
			$query = $query . " ORDER BY vehicle.VIN";

		}

//		echo $query;


		$result = mysqli_query($db, $query);

		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to find vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have that in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');
	} else {
		$query = "SELECT vehicle.VIN, vehicle.Types, vehicle.Year, vehicle.Manufacturer, vehicle.Model, a.CColors, vehicle.Odometer, buy.`Vehicle Description`, buy.`Purchase Price`, repair.`Repair Cost`, repair.`Repair ID`, repair.`Repair Status`, b.totalrepaircost
		FROM vehicle INNER JOIN 
		(SELECT VIN, group_concat(relationcolorvehicle.Colors separator ',') AS CColors FROM relationcolorvehicle GROUP BY VIN) a ON a.VIN = vehicle.VIN
		LEFT JOIN repair ON vehicle.VIN = repair.VIN 
		INNER JOIN buy ON vehicle.VIN = buy.VIN
		LEFT JOIN (SELECT VIN, SUM(`Repair Cost`) totalrepaircost FROM repair GROUP BY VIN) b ON vehicle.VIN = b.VIN ORDER BY vehicle.VIN";
		$result = mysqli_query($db, $query);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

		//number of vehicle under repair not in sell
		$query2 = "SELECT COUNT(DISTINCT(VIN)) AS VNISUR FROM (SELECT v.VIN FROM vehicle AS v RIGHT JOIN repair AS r ON r.VIN = v.VIN WHERE (r.`Repair Status` = 'in progress' OR r.`Repair Status` = 'pending') AND v.VIN NOT IN (SELECT VIN FROM sell)) c ";
		$result2 = mysqli_query($db, $query2);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');

		//number of vehicle not in sell
		$query3 = "SELECT COUNT(DISTINCT(VIN)) AS VNIS FROM vehicle WHERE vehicle.VIN NOT IN (SELECT VIN FROM sell)";
		$result3 = mysqli_query($db, $query3);
		if (mysqli_affected_rows($db) == -1) {
			array_push($error_msg,  "SELECT ERROR:Failed to pull vehicles ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		if (mysqli_affected_rows($db) == 0) {
			array_push($error_msg,  "Sorry, it looks like we don’t have any vehicle in stock! ... <br>" . __FILE__ ." line:". __LINE__ );
		}
		include('lib/show_queries.php');


	}
}


}
?>

<?php include('lib/show_queries.php');?>

<?php include("lib/header.php"); ?>

<title>Vehicle Search</title>
</head>

<body>
<!--	
<p id="myP1">This is some text.</p>
<p id="myP2">This is some text.</p>

<input type="button" onclick="demoDisplay()" value="Hide text with display property">
<input type="button" onclick="demoVisibility()" value="Hide text with visibility property">

<script>
function demoDisplay() {
  document.getElementById("myP1").style.display = "none";
}

function demoVisibility() {
  document.getElementById("myP2").style.visibility = "hidden";
}
</script>
-->
<?php 
function unique_multidim_array($array, $key) { 
	$temp_array = array(); 
	$i = 0; 
	$key_array = array(); 

	foreach($array as $val) { 
		if (!in_array($val[$key], $key_array)) { 
			$key_array[$i] = $val[$key]; 
			$temp_array[$i] = $val; 
		} 
		$i++; 
	} 
	return $temp_array; 
} 

?> 
<div id="main_container">

	<div class="center_content">
		<div class="center_left">
			<div class="title_name"><?php print 'Vehicle Search'; ?></div>                      
			<div class="features">   

				<div class="profile_section">                       
					<div class="subtitle">Search for Vehicles</div> 

					<form name="searchform" action="search_inventory.php" method="POST">
						<table>                             
							<tr>
								<td class="item_label">Vehicle Type:</td>
								<td>
									<select name="typename">                                                                                
										<option value="">--Please choose an option--</option>
										<?php 
										$sql = mysqli_query($db, "SELECT Types FROM type");
										while ($row = $sql->fetch_assoc()){
											echo "<option value=" . $row['Types'] .">" . $row['Types'] . "</option>";}
											?>
										</select>

									</td>
								</tr>
								<tr>
									<td class="item_label">Manufacturer:</td>
									<td>
										<select name="manufacturername">
											<option value="">--Please choose an option--</option>
											<?php 
											$sql = mysqli_query($db, "SELECT Manufacturer FROM manufacturer");
											while ($row = $sql->fetch_assoc()){
												echo "<option value=" . $row['Manufacturer'] .">" . $row['Manufacturer'] . "</option>";}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="item_label">Model Year:</td>
										<td>
											<select name="modelyear">
												<option value="">--Please choose an option--</option>
												<?php for($x = 2020; $x >= 1930; $x--) {
													echo "<option value='{$x}'>{$x}</option>";
												} ?>
											</select>
										</select>
									</td>
								</tr>
								<tr>
									<td class="item_label">Color:</td>
									<td>
										<select name="colorname">
											<option value="">--Please choose an option--</option>
											<?php 
											$sql = mysqli_query($db, "SELECT Colors FROM color");
											while ($row = $sql->fetch_assoc()){
												echo "<option value=" . $row['Colors'] .">" . $row['Colors'] . "</option>";}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="item_label">Key Word:</td>
										<td><input type="text" name="keyword" /></td>
									</tr>

									<tr <?php if(!isset($_SESSION['Role'])) {
										echo "style='display: none;'";
									} else {
										echo "style='display: block;' class='active'";
									} ?> >
									<td class="item_label">VIN:</td>
									<td><input type="text" name="vin" /></td>
								</tr>

								<tr <?php if(!isset($_SESSION['Role']) or $_SESSION['Role'] === 'Sales Person' or $_SESSION['Role'] === 'Manager') {
									echo "style='display: none;'";
								} else {
									echo "style='display: block;' class='active'";
								} ?> >
								<td class="item_label">Pull Purchase Vehicle:</td>
								<td>
									<select name="purchasedvehicle">
										<option value="">--No--</option>
										<option value="yes">--Yes--</option>
									</select>
								</td>
							</tr>

							<tr <?php if(!isset($_SESSION['Role']) or $_SESSION['Role'] === 'Sales Person' or $_SESSION['Role'] === 'Clerk') {
									echo "style='display: none;'";
								} else {
									echo "style='display: block;' class='active'";
								} ?> >
								<td class="item_label">Vehicle filter:</td>
								<td>
									<select name="vehiclesoldornot">
										<option value="">--All Vehicle--</option>
										<option value="unsold">--Unsold Vehicle--</option>
										<option value="sold">--Sold Vehicle--</option>
									</select>
								</td>
							</tr>

						</table>
						<a href="javascript:searchform.submit();" class="fancy_button">Search</a>                   
					</form>                         
				</div>

				<div class='profile_section'>
					<div class='subtitle'>Search Results</div>
					<table>
						<tr <?php if(!isset($result2)) {
							echo "style='display: none;'";
						} else {
							echo "style='display: inline;' class='active'";
						} ?> >
						<td class="item_label">Number of vehicles currently under repair:</td>

						<?php 
						$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

						print "<td>{$row2['VNISUR']}</td>";				

						?>
					</tr>

					<tr <?php if(!isset($result3)) {
						echo "style='display: none;'";
					} else {
						echo "style='display: inline;' class='active'";
					} ?> >
					<td class="item_label">Number of vehicles available for sale:</td>

					<?php 
					$row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC);
					$numberofvehicleforsale = ((float)$row3['VNIS'] - (float)$row2['VNISUR']);
					print "<td>{$numberofvehicleforsale}</td>";	
					
					?>
				</tr>

				<!--for manager-->
				<tr <?php if($_SESSION['Role'] != 'Manager') {
							echo "style='display: none;'";
						} else {
							echo "style='display: inline;' class='active'";
						} ?> >
						<td class="item_label">Number of vehicles currently under repair:</td>

						<?php 
						$query2 = "SELECT COUNT(DISTINCT(VIN)) AS VNISUR FROM (SELECT v.VIN FROM vehicle AS v RIGHT JOIN repair AS r ON r.VIN = v.VIN WHERE (r.`Repair Status` = 'in progress' OR r.`Repair Status` = 'pending') AND v.VIN NOT IN (SELECT VIN FROM sell)) c ";
						$result2 = mysqli_query($db, $query2);
						$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

						print "<td>{$row2['VNISUR']}</td>";				

						?>
					</tr>

					<tr <?php if($_SESSION['Role'] == 'Clerk' or $_SESSION['Role'] == 'All Roles') {
						echo "style='display: none;'";
					} else {
						echo "style='display: inline;' class='active'";
					} ?> >
					<td class="item_label">Number of vehicles available for sale:</td>

					<?php 
					$query3 = "SELECT COUNT(DISTINCT(VIN)) AS VNIS FROM vehicle WHERE vehicle.VIN NOT IN (SELECT VIN FROM sell)";
					$result3 = mysqli_query($db, $query3);
					$row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC);
					$numberofvehicleforsale = ((float)$row3['VNIS'] - (float)$row2['VNISUR']);
					print "<td>{$numberofvehicleforsale}</td>";	
					
					?>
				</tr>


				<tr>
					<td class='subtitle'>VIN</td>
					<td class='subtitle'>Vehicle type</td>
					<td class='subtitle'>Model Year</td>
					<td class='subtitle'>Manufacturer</td>
					<td class='subtitle'>Model</td>
					<td class='subtitle'>Colors</td>
					<td class='subtitle'>Mileage</td>
					<td class='subtitle'>Sales Price</td>
				</tr>
				<?php
				if (isset($result)) {
					$temp_var = 'String'; 

					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						if ($temp_var != $row['VIN'] and ($row['Repair Status'] == 'completed' or is_null($row['Repair Status']))){
							$temp_var = $row['VIN'];
							$search_VIN = urlencode($row['VIN']);
							$search_type = urlencode($row['Types']);
							$search_year = urlencode($row['Year']);
							$search_manufacturer = urlencode($row['Manufacturer']);
							$search_model = urlencode($row['Model']);
							$search_color = urlencode($row['CColors']);
							$search_mileage = urlencode($row['Odometer']);
							$search_description = urlencode($row['Vehicle Description']);
							$search_buyprice = $row['Purchase Price'];
							$search_saleprice = sprintf('%0.2f',1.25 * (float)$row['Purchase Price'] + 1.1*(float)$row['totalrepaircost']);
							print "<tr>";
							print "<td><a href='view_vehicle_details.php?search_VIN=$search_VIN&search_description=$search_description' target=\"_blank\">{$row['VIN']}</a></td>";
							print "<td>{$row['Types']}</td>";
							print "<td>{$row['Year']}</td>";
							print "<td>{$row['Manufacturer']}</td>";
							print "<td>{$row['Model']}</td>";
							print "<td>{$row['CColors']}</td>";
							print "<td>{$row['Odometer']}</td>"; 
							print "<td>{$search_saleprice}</td>";                                  
							print "</tr>";
						} 
					}
				}   ?>
			</table>


		</div> 
	</div> 

	<?php include("lib/error.php"); ?>

	<div class="clear"></div> 
</div>    


</div>
</body>
</html>
