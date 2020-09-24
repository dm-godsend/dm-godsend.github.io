<?php

	include 'db_connect_test.php';

	$out["flag"] = 0;
	$out["usr"] = array();

	// $vk_id = $_GET["vk_id"];
	//webapi:Request("get_user", {id = 1})
	if (isset($_GET["name"])) {
		$name = $_GET["name"];
		$query = "SELECT * FROM `users` WHERE `name`='$name'";
	}
	if (isset($_GET["id"])) {
		$id = $_GET["id"];
		$query = "SELECT * FROM `users` WHERE `id`='$id'";
	}
	// if (isset($vk_id))  $query = "SELECT * FROM `users` WHERE `vk_id`='$vk_id'";
	//В обоих запросах добавить условие на сортировку по классам!!!
	if ($res = $mysqli->query($query))
	{
		$res_date = $mysqli->query("SELECT * FROM `time`");
		$row_date = $res_date->fetch_assoc();
		//Локальная дата в игре, получаемая из базы, должна задаваться только при входе в игру
		// А время каждый раз можно править при загрузке чего-либо
		$out["date"] = $row_date;

		//В row находится информация о пользователе
		if ($row = $res->fetch_assoc())
		{

			$out["flag"] = 1;
			$out["msg"] = "Successful download user info";
			$id = $row["id"]; // потому что изначально мог искать по name и id не задана

			$bday = $row["bday"];
			$newDate = date("m-d-Y", strtotime($bday));
			$row["bday"] = $newDate;

			//ЗАГРКЗКА ИНФОРМАЦИИ О КВАРТИРЕ
			$res_apart = $mysqli->query("SELECT * FROM `user_apart`  WHERE `user_id`='$id'");
			if ($row_apart = $res_apart->fetch_assoc())
			{
				$row["room"] = $row_apart;
			}

			$quest_id = $row["pquest"];
			//ЗАГРКЗКА ИНФОРМАЦИИ О ТЕКУЩЕМ ВОПРОСЕ ПРИНЦА
			$res_quest = $mysqli->query("SELECT * FROM `questions` WHERE `ord`='$quest_id' AND `type` = 1 ");
			if ($row_quest = $res_quest->fetch_assoc())
			{
				$res1 = $mysqli->query("SELECT COUNT(*) as cnt FROM `questions` WHERE `type` = 1");
				$row1 = $res1->fetch_assoc();

				$row["pquestCnt"] = $row1["cnt"];
				$row["prince_quest"] = $row_quest;
			}

			$quest_id = $row["gquest"];
			//ЗАГРКЗКА ИНФОРМАЦИИ О ТЕКУЩЕМ ВОПРОСЕ БАБКИ
			$res_quest = $mysqli->query("SELECT * FROM `questions`  WHERE `ord`='$quest_id' AND `type` = 2 ");
			if ($row_quest = $res_quest->fetch_assoc())
			{
				$res1 = $mysqli->query("SELECT COUNT(*) as cnt FROM `questions` WHERE `type` = 2");
				$row1 = $res1->fetch_assoc();

				$row["gquestCnt"] = $row1["cnt"];
				$row["grand_quest"] = $row_quest;
			}

			//ЗАГРКЗКА ИНФОРМАЦИИ О РАБОТЕ
			$res_work = $mysqli->query("SELECT *, `user_work`.`id` as wid FROM `user_work` LEFT JOIN `employers` ON `user_work`.`emp_id` = `employers`.`id` WHERE `user_id`='$id'");
			while ($row_work = $res_work->fetch_assoc())
			{
				$row_work["id"] = $row_work["wid"];
				if ( $row_work["emp_id"] < 6 )
					$row["work"] = $row_work;
				else
					$row["sawmill"] = $row_work;
			}

			//ЗАГРУЗКА ПАРАМЕТРОВ ПОЛЬЗОВАТЕЛЯ
			$res_param = $mysqli->query("SELECT * FROM `user_params` WHERE `user_id`='$id'");
			if ($row_param = $res_param->fetch_assoc())
			{
				$row["params"] = $row_param;
			}

			//ЗАГРУЗКА ИНФОРМАЦИИ ОБ ИП
			$res_ip = $mysqli->query("SELECT * FROM `user_ip` WHERE `user_id`='$id'");
			if ($row_ip = $res_ip->fetch_assoc())
			{
				$ip_id = $row_ip["id"];

				//Формирования списка на складе
				$res_resources = $mysqli->query("SELECT * FROM `ip_resources` WHERE `ip_id`='$ip_id' ");
				$chest = array();
				while ($row_res = $res_resources->fetch_assoc())
				{
					//Заполнение массива сундука данными
					$chest[$row_res["res_id"]-1][$row_res["stage"]-1] = $row_res["amount"];
				}

				//Формирование списка рабочих
				$res_workers = $mysqli->query("SELECT * FROM `ip_workers` LEFT JOIN `users` ON `users`.`id` = `ip_workers`.`user_id` WHERE `ip_id`='$ip_id' ");
				$row_ip["labor"] = array();
				while ($row_workers = $res_workers->fetch_assoc())
					array_push($row_ip["labor"], $row_workers);
				//if (count($row_ip["labor"]) == 0) $row_ip["labor"] = NULL;

				//Формирование списка соискателей (ещё не нанятые боты)
				$res_seekers = $mysqli->query("SELECT * FROM `users` WHERE `status` = 'bot' AND `id` NOT IN (select `user_id` from `ip_workers` where `ip_id` = '$ip_id')");
				$row_ip["seekers"] = array();
				while ($row_seekers = $res_seekers->fetch_assoc())
					array_push($row_ip["seekers"], $row_seekers);
				//if (count($row_ip["seekers"]) == 0) $row_ip["seekers"] = NULL;

				//Присоединение налогов к ИП при наличии на выходе
				$res_tax = $mysqli->query("SELECT *,`ip_tax`.`id` as 'ipTaxId' FROM `ip_tax` LEFT JOIN `taxes` ON `taxes`.`id` = `ip_tax`.`tax_id` WHERE `ip_id` = $ip_id");
				$row_ip["tax"] = array();
				while ($row_tax = $res_tax->fetch_assoc())
				{
					$row_tax["id"] = $row_tax["ipTaxId"];
					array_push($row_ip["tax"], $row_tax);
				}
				//if (count($row_ip["tax"]) == 0) $row_ip["tax"] = NULL;

				//Присоединение текущих продаж на рынке старше дня
				$res_market = $mysqli->query("SELECT * FROM `market` WHERE `ip_id` = $ip_id AND `stat` = 0");
				$row_ip["sales"] = array();
				while ($row_sales = $res_market->fetch_assoc())
				{
					//Добавляю непокупаемые больше дня товары
					if (( intval(date("md")) - intval(date("md",strtotime($row_sales["updated"]))) ) > 1)
						array_push($row_ip["sales"], $row_sales);
				}
				if (count($row_ip["sales"]) == 0) $row_ip["sales"] = NULL;

				//Запись текущего предприятия
				$row["ip"] = $row_ip;
				//Обновление данных сундука
				$row["chest"] = $chest;
			}

			//ЗАГРУЗКА КРЕДИТОВ/ДЕПОЗИТОВ //В том числе загружается id в таблице  user_oper, по которому можно и удалить
			$res_oper = $mysqli->query("SELECT *, `user_oper`.`id` as oid FROM `user_oper` LEFT JOIN `bank_opers` ON `user_oper`.`oper_id` = `bank_opers`.`id` WHERE `user_id`='$id'");
			while ($row_oper = $res_oper->fetch_assoc())
			{
				if ($row_oper["oper_id"] <=3 ) $oper = "credit"; else $oper ="deposit";// = $row_oper;
				$row_oper["id"] = $row_oper["oid"];
				$row[$oper] = $row_oper;
			}

			//ЗАГРКЗКА ИНФОРМАЦИИ О НАВЫКАХ И БОНУСАХ СУММАРНЫХ
			$res_abil = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id`='$id'");
			$row["ability"] = array();

			//Тут же можно учитывать бонусы к абилкам от ачивок, перебирая achievements по ability_id
			while ($row_abil = $res_abil->fetch_assoc())
			{
				$row_abil["value"] = 0; // Для полного пересчёта
				array_push($row["ability"], $row_abil);
				// Обнуление в базе
				$mysqli->query("UPDATE `user_ability` SET `value` = 0 WHERE `user_id` = '$id';");
			}
			$ability = $row["ability"];

			//ЗАГРУЗКА ИНФОРМАЦИИ О СКИЛАХ, ВНУТРИ ПРИБАВКА ТОЛЬКО ПРИ ЗАГРУЗКЕ (ТО, ЧТО БОНУСАМИ)
			$res_bonus = $mysqli->query("SELECT *, `user_bonus`.`id` as bid FROM `user_bonus` LEFT JOIN `skill_bonus` ON `user_bonus`.`bonus_id` = `skill_bonus`.`id` WHERE `user_id`='$id'");
			$skills = array(0,0,0,0);
			$row["bonuses"] = array();

			//подсчёт количества активных бонусов каждого скила
			while ($row_bonus = $res_bonus->fetch_assoc())
			{
				$skills[$row_bonus["skill_id"]-1] = $skills[$row_bonus["skill_id"]-1] + 1;
				$row_bonus["id"] = $row_bonus["bid"]; //записываем как бы не id бонуса, а id записи об этом бонусе у пользователя

				array_push($row["bonuses"], $row_bonus);

				// Прибавка бонусов от скиллов
				$ability_id = $row_bonus["ability_id"];
				$value = $row_bonus["amount"];

				//Увеличение навыка, указанного в ресурсе
				$ability[$ability_id-1]["value"] += $value;
			}
			$row["skills"] = $skills;

			//Предметы /бонусы /шмотки на складе
			$res_stock = $mysqli->query("SELECT *, `user_stock`.`id` as 'usID' FROM `user_stock` LEFT JOIN `stock_items` ON `stock_items`.`id` = `user_stock`.`item_id` WHERE `user_id`='$id'");
			$row["stock"] = array();
			while ($row_stock = $res_stock->fetch_assoc())
			{
				$row_stock["id"] = $row_stock["usID"];
				array_push($row["stock"], $row_stock);
			}

			//Увеличение скилов
			for ($i=0; $i<count($ability); $i++)
			{
				$ability_id = $ability[$i]['ability_id'];
				$value = $ability[$i]['value'];

				$mysqli->query("UPDATE `user_ability` SET `value` = $value WHERE `user_id` = '$id' AND `ability_id` = $ability_id;");
			}
			//Возвращение вновь пересчитанных данных о навыках
			$row["ability"] = $ability;
			// КОНЕЦ ЗАГРУЗКИ СКИЛЛОВ И ПЕРЕСЧЕТА НАВЫКОВ ОТ СКИЛЛОВ
			// НИЖЕ ПРОДЛЖЕНИЕ ПОДСЧЁТА ТОГО, ЧТО МЕНЯЕТ ИТОГОВОЕ ЗНАЧЕНИЕ НАВЫКОВ (ОДЕЖДА и ПР)

			//ЗАГРКЗКА ИНФОРМАЦИИ О АЧИВКАХ (ОБЩИЙ СЛУЧАЙ УЧЁБЫ)
			$res_achieve = $mysqli->query("SELECT * FROM `user_achieve` WHERE `user_id`='$id'");
			$row["achieve"] = array();
			while ($row_achieve = $res_achieve->fetch_assoc())
			{
				array_push($row["achieve"], $row_achieve);
			}

			if (count($row["achieve"])>0) // Если что-то загрузилось
				//В учёбу записана первая доступная ачивка в списке пользователя
				$row["study"] = $row["achieve"][0];
			// else
			// 	$row["achieve"] = null;
			//Запись последнего, в нашем случаи при одной ачивке это и есть учёба

			//ЗАГРКЗКА ЖУРНАЛА СОБЫТИЙ (50 последних)
			$res_journal = $mysqli->query("SELECT * FROM `journal` WHERE `user_id`='$id' ORDER BY `updated` DESC LIMIT 0,30");
			$row["journal"] = array();
			while ($row_journal = $res_journal->fetch_assoc())
			{
				if (( intval(date("ymd")) - intval(date("ymd",strtotime($row_journal["created"]))) ) < 30)
					{
						$row_journal["date"] = timestampToLocal($mysqli, $row_journal["created"]);
						array_push($row["journal"], $row_journal);
					}
			}

			//Возвращение обзего объекта со всеми изменениями
			$out["usr"] = $row;

		}
		// КОНЕЦ ЗАГРУЗКИ ДАННЫХ СУЩЕСТВУЮЩЕГО ПОЛЬЗОВАТЕЛЯ
		else
		{
			$out["flag"] = 0;
			$out["msg"] = "No such user";
			$out["usr"] = "NEW";
			// ТАКОГО ПОЛЬЗОВАТЕЛЯ НЕТ, СОЗДАНИЕ СВОЕГО
			// if (isset($name)) // только если запрашивал по имени, создать нового при ненахождение
			// {
			// 	$qwery = "INSERT INTO `users` (`id`, `ava`, `name`, `gender`, `appear`, `status`, `magic`, `exp`, `health`, `joy`, `coins`, `lvl`, `points`, `help`, `bday`) VALUES (NULL, 'ava.png', '', 'female', '1 1 1', 'Физическое лицо', 'не обучен', '0', '0', '0.85', '1500', '1', '0', '5', NULL);";
			//
			// 	$res = $mysqli->query($qwery);
			// 	$res = $mysqli->query("SELECT * FROM `users` ORDER BY `id` DESC");
			//
			// 	$row = $res->fetch_assoc(); //первый элемент с наибольшим id
			// 	//после полного цикла в $row - последний (т.е добавленный только что)
			//
			// 	//стандартные параметры нового пользователя (те, что обычно генерируются при загрузке)
			// 	$row["skills"] = array(0,0,0,0);
			// 	//Это означает, что пользователь новый и это его первый вход
			// 	$row["first"] = true;
			//
			// 	$out["usr"] = $row;
			//
			// 	$out["flag"] = 1;
			// 	$out["msg"] = "Successful adding user";
			//
			// }
		}

	}

	// $tm = time();
	// $nowdt = mktime( 0,0,0,date( "m",$tm ),date( "d",$tm ),date("y",$tm ) );
	// $start_day = ($tm - $nowdt); //%(3600*24)+date("Z"); // кол-во секунд с начала дня
	// //echo "START: ".$start_day;
	// $out["time"] = $start_day;

	echo json_encode($out);
?>
