<?php

	include 'db_connect.php';

	$out["flag"] = 0;
	$id = $_POST["id"];
	$hard = $_POST["hard"];

	if (isset($id))
	{
		$mysqli->query("DELETE FROM `user_params` WHERE `user_id` = '$id';");
		$mysqli->query("DELETE FROM `user_oper` WHERE `user_id` = '$id';");
		$mysqli->query("DELETE FROM `user_apart` WHERE `user_id` = '$id';");
		$mysqli->query("DELETE FROM `user_achieve` WHERE `user_id` = '$id';");
		$mysqli->query("DELETE FROM `user_work` WHERE `user_id` = '$id';");
		$mysqli->query("DELETE FROM `user_bonus` WHERE `user_id` = '$id';");

		$mysqli->query("DELETE FROM `journal` WHERE `user_id` = '$id';");

		if ($res = $mysqli->query("SELECT `id` FROM `user_ip` WHERE `user_id` = '$id';"))
		{
			while ($row = $res->fetch_assoc())
			{
				$ip_id = $row["id"];
				$mysqli->query("DELETE FROM `ip_resources` WHERE `ip_id` = '$ip_id';");
				$mysqli->query("DELETE FROM `ip_tax` WHERE `ip_id` = '$ip_id';");
				$mysqli->query("DELETE FROM `ip_workers` WHERE `ip_id` = '$ip_id';");
				$mysqli->query("DELETE FROM `market` WHERE `ip_id` = '$ip_id';");
			}
		}
		//Чтобы не удалять Гос ИП, а потом переносить их на другой АКК
		$mysqli->query("DELETE FROM `user_ip` WHERE `user_id` = '$id' AND `user_id` > 1;");

		if (isset($hard))
		{
			$mysqli->query("DELETE FROM `user_ability` WHERE `user_id` = '$id';");
			$mysqli->query("DELETE FROM `user_stock` WHERE `user_id` = '$id';");
			$mysqli->query("DELETE FROM `users` WHERE `id` = '$id';");
		}
		else
		{
			$mysqli->query("UPDATE `user_ability` SET `value` = 0 WHERE `user_id` = '$id';");
			$mysqli->query("DELETE FROM `user_stock` WHERE `user_id` = '$id' AND `item_id` <> 1 AND `item_id` <> 3 AND `item_id` <> 8;");
			$mysqli->query("UPDATE `user_stock` SET `is_active` = '1' WHERE `user_id` = '$id';");

			$mysqli->query("INSERT INTO `user_params` (`id`, `user_id`, `lang`, `sound`, `music`, `note`, `game`, `employ`, `treasure`, `mine`, `room`, `bank`, `tax`, `sambl`, `portal`, `grandma`, `school`, `story_id`, `current_id`, `mom`,`work_msg`,`scene`, `created`, `updated`) VALUES (NULL, '$id', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '-1', '1','0','game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);");
			$mysqli->query("UPDATE `users` SET `magic` = '0', `exp` = '0', `coins` = '1500', `lvl` = '1', `points` = '0', `joy` = '1', `status` = 'Физическое лицо', `status_id` = '1' WHERE `users`.`id` = '$id';");
		}

	}

	echo json_encode($out);

?>
