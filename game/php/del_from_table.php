<?php

	include 'db_connect.php';

	$out["flag"] = 0;
	//$out["usr"] = array();
	//$out["time"]= date("H:i");

   $id = $_POST["id"];
	$table = $_POST["table"];

	$qwery = "DELETE FROM `$table` WHERE `id` = '$id';";

	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($mysqli->query($qwery) == TRUE) // не работает
	{
		$out["flag"] = 1;
		//Понять как точнее определять успешный запрос, результат из базы
		$out["msg"] = "Successfully delete info from ".$table;
	}

	echo json_encode($out);

?>
