<?php

	include 'db_connect.php';	

	$out["flag"] = 0;
	$out["usr"] = array();
	$out["time"]= date("H:i");
	
    $id = $_POST["id"];
	$coins = $_POST["coins"];

	$qwery = "UPDATE `users` SET `coins` = `coins`+'$coins' WHERE `id` = '$id';";
	
		
	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($mysqli->query($qwery) == TRUE)
	{
		$out["flag"] = 1;
		//Понять как точнее определять успешный запрос, результат из базы
		$out["msg"] = "Successfully update coins info ";
	}

	echo json_encode($out);
	
?>

