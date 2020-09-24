<?php

	include 'db_connect.php';

	$out["flag"] = 0;
	//$out["time"]= date("H:i");

	//Если работаем с личным счётом пользователя, так удобнее
	$user_id = $_POST["id"];
	if (!isset($user_id)) $user_id = $_POST["user_id"];

	$ip_id = $_POST["ip_id"];
	$res_id = $_POST["res_id"];
	$stage = $_POST["stage"];
	$quantity = $_POST["quantity"];
	$cost = $_POST["cost"];

	// $user_id = $_GET["id"];
	//
	// $ip_id = $_GET["ip_id"];
	// $res_id = $_GET["res_id"];
	// $stage = $_GET["stage"];
	// $quantity = $_GET["quantity"];
	// $cost = $_GET["cost"];

	$qwery_market = "INSERT INTO `market` (`id`, `ip_id`, `res_id`, `stage`, `quantity`, `cost`, `stat`, `created`, `updated`) VALUES (NULL, '$ip_id', '$res_id', '$stage', '$quantity', '$cost', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";

	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($mysqli->query($qwery_market) == TRUE)
	{
		$out["flag"] = 1;
		//Понять как точнее определять успешный запрос, результат из базы
		$out["msg"] = "Successfully adding market operation sell";

		// Изменение кол-ва ресурса в сундуке предприятия
		if (( isset($stage) )&&( isset($res_id) )&&( isset($quantity) ))
			$mysqli->query("UPDATE `ip_resources` SET `amount` = `amount` - $quantity  WHERE `ip_id` = '$ip_id' AND `res_id` = '$res_id' AND `stage` = '$stage';");

	}

	echo json_encode($out);

?>
