<?php

	include 'db_connect_test.php';

	$out["flag"] = 0;
	$out["market"] = array();

	$qwery = "SELECT *,`market`.`res_id` as 'rid', `market`.`id` as 'mid' FROM `market` LEFT JOIN `user_ip` ON `user_ip`.`id` = `market`.`ip_id` WHERE `stat` = 0 ORDER BY `stage` ASC, `market`.`res_id` ASC, `updated` DESC LIMIT 0, 50"; // `cost` ASC,

	if($result = $mysqli->query($qwery)) {
	//	printf("Select вернул %d строк.\n", $result->num_rows);
		while( $row = $result->fetch_assoc())
			{
				$row["res_id"] = $row["rid"];
				$row["id"] = $row["mid"];

				// $user_id = $_GET["id"];
				// //Учёт скидки, только если задан id в запросе
				// if (isset($user_id))
				// {
				// 	//Получение списка навыков покупателя (чтобы учесть скидки на рынке)
				// 	$res_ability = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id` = '$user_id';");
				// 	$abil = array();
				// 	while ($row_ability = $res_ability->fetch_assoc())
				// 		array_push($abil, $row_ability);
				// 	//Навык понижения цены на рынке + всех цен
				// 	$discount = $abil[7]["value"] + $abil[8]["value"];
				// 	$row["cost"] = $row["cost"]*(1 - $discount);
				// }
				array_push($out["market"], $row);
			}
		$out["flag"] = 1;
		$out["msg"] = "Successfully get market info ";

		$result->close();
	}

	echo json_encode($out);

?>
