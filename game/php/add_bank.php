<?php

	include 'db_connect.php';

	$out["flag"] = 0;
	$out["usr"] = array();
	//$out["time"]= date("H:i");

	$user_id = $_POST["uid"];
	$oper_id = $_POST["oid"];

	$time = $_POST["time"];
	$days = $_POST["days"];
	//$left = $_POST["left"]; при добавлении равно 0
	$coins = $_POST["coins"];
	$balance = $_POST["balance"];
	$pay = $_POST["pay"];
	$start = $_POST["start"];


	$qwery = "INSERT INTO `user_oper` (`id`, `user_id`, `oper_id`, `time`, `days`, `left`, `coins`, `balance`, `pay`, `start`) VALUES (NULL, '$user_id', '$oper_id', '$time', '$days', '0', '$coins', '$balance', '$pay', '$start')";

	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($mysqli->query($qwery) == TRUE)
	{
		$out["flag"] = 1;
		//Понять как точнее определять успешный запрос, результат из базы
		$out["msg"] = "Successfully adding bank operation ";

		$qwery_get = "SELECT * FROM `user_oper` WHERE `user_id` = '$user_id' AND `oper_id` = '$oper_id'";
		$res_oper = $mysqli->query($qwery_get);
		$row_oper = $res_oper->fetch_assoc();

		$out["oper"] = $row_oper;
	}

	echo json_encode($out);

?>
