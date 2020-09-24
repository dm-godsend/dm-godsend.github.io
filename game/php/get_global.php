	<?php

	include 'db_connect_test.php';

	$out["flag"] = 0;
	$out["msg"] = "";
	$row = array();

	if ( $res_stages = $mysqli->query("SELECT * FROM `stages`;") )
	{
		$row["stages"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_stages = $res_stages->fetch_assoc())
			array_push($row["stages"], $row_stages);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get stages info";
	}

	if ( $res_resources = $mysqli->query("SELECT * FROM `resources`;") )
	{
		$row["resources"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_resources = $res_resources->fetch_assoc())
			array_push($row["resources"], $row_resources);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get resources info";
	}

	if ( $res_employers = $mysqli->query("SELECT * FROM `employers`;") )
	{
		$row["employers"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_employers = $res_employers->fetch_assoc())
			array_push($row["employers"], $row_employers);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get employers info";
	}

	if ( $res_bonus = $mysqli->query("SELECT * FROM `skill_bonus`;") )
	{
		$row["bonuses"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_bonus = $res_bonus->fetch_assoc())
			array_push($row["bonuses"], $row_bonus);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get skill bonus info";
	}

	if ( $res_tools = $mysqli->query("SELECT * FROM `tools`;") )
	{
		$row["tools"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_tools = $res_tools->fetch_assoc())
			array_push($row["tools"], $row_tools);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get tools info";
	}

	if ( $res_taxes = $mysqli->query("SELECT * FROM `taxes`;") )
	{
		$row["taxes"] = array();
		//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row_taxes = $res_taxes->fetch_assoc())
			array_push($row["taxes"], $row_taxes);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get taxes info";
	}

	if ( $res_stock = $mysqli->query("SELECT * FROM `stock_items` ORDER BY `type_id` ASC, `place_id` ASC;") )
	{
		$row["stock"] = array();
		while ($row_stock = $res_stock->fetch_assoc())
			array_push($row["stock"], $row_stock);

		$out["flag"] = 1;
		$out["msg"] .= " Successfully get stock items info";
	}

	$out["global"] = $row;

	echo json_encode($out);

	?>
