	<?php

	include 'db_connect.php';

	$out["flag"] = 0;

	//Данные покупателя и кол-ва, в последующем на счет ip будет падать
	$user_id = $_POST["id"];
	if (!isset($user_id)) $user_id = $_POST["user_id"];
	$ip_id = $_POST["ip_id"];
	$amount = $_POST["amount"];

	//Для получения всех данных продавца
	$market_id = $_POST["market_id"];

	// $user_id = $_GET["id"];
	// $ip_id = $_GET["ip_id"];
	// $amount = $_GET["amount"];
	//
	// //Для получения всех данных продавца
	// $market_id = $_GET["market_id"];

	// Можно добавить проверку на
	$qwery_market = "SELECT *,`market`.`res_id` as 'rid'  FROM `market` LEFT JOIN `user_ip` ON `user_ip`.`id` = `market`.`ip_id` WHERE `stat` = 0 AND `quantity` >= '$amount' AND `market`.`id` = '$market_id';";

	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($res_market = $mysqli->query($qwery_market))
	{
		if ($row_market = $res_market->fetch_assoc())
		{
			//Необходимые данные о продавце
			$seller_ip = $row_market["ip_id"];
			$seller_user = $row_market["user_id"];
			//Сколько всего в покупке, обрабатывать только amount
			$sell_quant = $row_market["quantity"];
			$sell_cost = $row_market["cost"];

			$coins = $amount*$sell_cost;

			$res_id = $row_market["rid"];
			$stage = $row_market["stage"];

			// Хотят купить не всё продаваемое
			// Изменение количества продаваемого, или статуса покупки на продано, если купили всё
			if ( $sell_quant > $amount )
				$mysqli->query("UPDATE `market` SET `quantity` = `quantity` - $amount, `updated` = CURRENT_TIMESTAMP WHERE `market`.`id` = '$market_id';");
			else
				$mysqli->query("UPDATE `market` SET `stat` = '1', `updated` = CURRENT_TIMESTAMP WHERE `market`.`id` = '$market_id';");

			// Изменение кол-ва ресурса в сундуке покупателя
			$mysqli->query("UPDATE `ip_resources` SET `amount` = `amount` + $amount  WHERE `ip_id` = '$ip_id' AND `res_id` = '$res_id' AND `stage` = '$stage';");

			//Получение списка навыков покупателя (чтобы учесть скидки на рынке)
			$res_ability = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id` = '$user_id';");
			$abil = array();
			while ($row_ability = $res_ability->fetch_assoc())
				array_push($abil, $row_ability);
			//Навык понижения цены на рынке + всех цен
			$discount = $abil[7]["value"] + $abil[8]["value"];
			$resCost = $coins*(1-$discount);
			// Изменение личного счёта покупателя с учётом скидок
			$mysqli->query("UPDATE `users` SET `coins` = `coins` - $resCost  WHERE `id` = '$user_id';");
			// Пока и на личный счёт и в капитал компании
			//$mysqli->query("UPDATE `user_ip` SET `capital` = `capital` - $coins  WHERE `user_ip`.`id` = '$ip_id';");

			// Изменение личного счёта продавца, в сундуке продавца изменения происходят при добавлении заказа
			$mysqli->query("UPDATE `users` SET `coins` = `coins` + $coins  WHERE `id` = '$seller_user';");
			// Тут увеличение доходности и общего оборота
			$mysqli->query("UPDATE `user_ip` SET `profit` = `profit` + '$coins', `total` = `total` + '$coins' WHERE `id` = '$seller_ip';");


			if ($res_stages = $mysqli->query("SELECT * FROM `stages`"))
			{
				while ($row_stages = $res_stages->fetch_assoc())
				{
					if ($row_stages["id"] == $stage) $stageName = $row_stages["name"]."а";
				}
			}

			if ($res_res = $mysqli->query("SELECT * FROM `resources`"))
			{
				while ($row_res = $res_res->fetch_assoc())
				{
					if ($row_res["id"] == $res_id) $resName = $row_res["name"];
				}
			}
			//Формирование индекса картинки
			$icon_index = "res_".$res_id.$stage.".png";
			$local_date = timestampToLocal($mysqli);
			//Генерация события в журнал, в журнале отметка, создаёт ли это событие сообщение, отметка прочитано
			$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
				VALUES (NULL, '$seller_user', 'Поздравляем, Вы успешно продали $amount ед. $stageName $resName за $coins р.', '0', '1', '$icon_index', 'staff', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");

			$out["flag"] = 1;
			//Понять как точнее определять успешный запрос, результат из базы
			$out["msg"] = "Successfully processing market buy";
		}
		else
		{
			// Такой рыночной операции уже нет или такого количества нет (произошли изменения в базе, пока совершал операцию)
			$out["flag"] = 0;
			$out["msg"] = "No such market item or no such quantity or invalid query parameters";
		}

	}
	else
	{
		// Такой рыночной операции уже нет или такого количества нет (произошли изменения в базе, пока совершал операцию)
		$out["flag"] = 0;
		$out["msg"] = "No such market item or no such quantity or invalid query parameters";
	}

	echo json_encode($out);

	?>
