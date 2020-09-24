<?php

	include 'db_connect_test.php';

	//$id = $_GET["id"];
	$lvl = $_GET["lvl"];
	$exp = $_GET["exp"];
	$stat_id = $_GET["status_id"];
	$race_profit = $_GET["race_profit"];

	$is_race = $_GET["is_race"];

	$out["flag"] = 0;
	$out["rating"] = array();
//	$out["time"] = date("H:i");

	if (isset($is_race))
	{
		$qwery = "SELECT `id`, `race_profit`,`ava`, `name`, `coins`, `exp`, `lvl` FROM `users` WHERE `status_id`='$stat_id'  AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 0,30";
		$qweryPosition = "SELECT count(*) as 'place' FROM `users` where `race_profit` > '$race_profit' AND `status_id`='$stat_id'";
	}
	else
	{
		$qwery = "SELECT `id`, `ava`, `name`, `coins`, `exp`, `lvl` FROM `users` ORDER BY `lvl` DESC, `exp` DESC LIMIT 0, 30";
		$qweryPosition = "SELECT count(*) as 'place' FROM `users` where `lvl` > '$lvl' or (`lvl` = '$lvl' and `exp` > '$exp') and `exp` > '$exp'";
	}

	if($result = $mysqli->query($qwery)) {
	//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row = $result->fetch_assoc())
			array_push($out["rating"], $row);

		$out["flag"] = 1;
		$out["msg"] = "Successfully get rating ";

		$result->close();
	}
	if (isset ($lvl) and isset ($exp)) {
		$result = $mysqli->query($qweryPosition);
		$row = $result->fetch_assoc();
		$userPosition = $row["place"]+1;
		$out["pos"] = $userPosition;
	}

	echo json_encode($out);

?>
