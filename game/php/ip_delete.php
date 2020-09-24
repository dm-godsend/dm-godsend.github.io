<?php

	include 'db_connect.php';

	$out["flag"] = 0;
	$id = $_POST["id"];

	if ($res = $mysqli->query("SELECT `id` FROM `user_ip` WHERE `user_id` = '$id';"))
	{
		//Удаление всех записей во всех таблицах, связанных с ИП пользователя
		while ($row = $res->fetch_assoc())
		{
			$ip_id = $row["id"];
			$mysqli->query("DELETE FROM `ip_resources` WHERE `ip_id` = '$ip_id';");
			$mysqli->query("DELETE FROM `ip_tax` WHERE `ip_id` = '$ip_id';");
			$mysqli->query("DELETE FROM `ip_workers` WHERE `ip_id` = '$ip_id';");
			$mysqli->query("DELETE FROM `market` WHERE `ip_id` = '$ip_id';");
		}

		$mysqli->query("DELETE FROM `user_ip` WHERE `user_id` = '$id';");
		$mysqli->query("UPDATE `users` SET `status` = 'Физическое лицо', `status_id` = '1', `motiv` = '0' WHERE `users`.`id` = '$id';");
		$mysqli->query("UPDATE `user_params` SET `deport` = '1' WHERE `user_id` = '$id';");
	}

	echo json_encode($out);

?>
