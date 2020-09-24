<?php

include 'db_connect.php';

$out["flag"] = 0;
$out["usr"] = array();
//$out["time"]= date("H:i");

$id = $_POST["id"];
if (isset($id))
{
	//ПАРАМЕТРЫ USER_PARAMS
	$param_id = $_POST["param_id"]; //Пока не используется, ибо у пользователя только одна работа

	// ТАБЛИЦА USER_PARAMS
	if (isset($param_id)) // Просто обновление ходов чаще всего
	{
		$lang = $_POST["lang"];
		$sound= $_POST["sound"];
		$music = $_POST["music"];
		$note = $_POST["note"];

		$game = $_POST["game"];
		$mine = $_POST["mine"];
		$employ = $_POST["employ"];
		$treasure = $_POST["treasure"];
		$room = $_POST["room"];
		$bank = $_POST["bank"];

		$tax = $_POST["tax"];
		$sambl = $_POST["sambl"];
		$school = $_POST["school"];
		$grandma = $_POST["grandma"];
		$portal = $_POST["portal"];
		$staff = $_POST["staff"];

		$story_id = $_POST["story_id"];
		$current_id = $_POST["current_id"];
		$story_left = $_POST["story_left"];

		$mom = $_POST["mom"];
		$patience = $_POST["patience"];
		$insure = $_POST["insure"];
		$deport = $_POST["deport"];
		//$debt = $_POST["debt"];

		$work_msg = $_POST["work_msg"];
		$motiv_msg = $_POST["motiv_msg"];
		$room_msg = $_POST["room_msg"];
		$tax_msg = $_POST["tax_msg"];
		$market_msg = $_POST["market_msg"];
		$insure_msg = $_POST["insure_msg"];

		$week_msg = $_POST["week_msg"];

		$race_stat = $_POST["race_stat"];
		$note_request = $_POST["note_request"];

		$scene = $_POST["scene"];

		$str_params = "";
		if (isset($lang)) $str_params = $str_params."`lang` = '$lang', ";
		if (isset($sound)) $str_params = $str_params."`sound` = '$sound', ";
		if (isset($music)) $str_params = $str_params."`music` = '$music', ";
		if (isset($note)) $str_params = $str_params."`note` = '$note', ";

		if (isset($game)) $str_params = $str_params."`game` = '$game', ";
		if (isset($mine)) $str_params = $str_params."`mine` = '$mine', ";
		if (isset($employ)) $str_params = $str_params."`employ` = '$employ', ";
		if (isset($room)) $str_params = $str_params."`room` = '$room', ";
		if (isset($tax)) $str_params = $str_params."`tax` = '$tax', ";
		if (isset($sambl)) $str_params = $str_params."`sambl` = '$sambl', ";

		if (isset($bank)) $str_params = $str_params."`bank` = '$bank', ";
		if (isset($school)) $str_params = $str_params."`school` = '$school', ";
		if (isset($treasure)) $str_params = $str_params."`treasure` = '$treasure', ";
		if (isset($grandma)) $str_params = $str_params."`grandma` = '$grandma', ";
		if (isset($portal)) $str_params = $str_params."`portal` = '$portal', ";
		if (isset($staff)) $str_params = $str_params."`staff` = '$staff', ";

		if (isset($story_id)) $str_params = $str_params."`story_id` = '$story_id', ";
		if (isset($current_id)) $str_params = $str_params."`current_id` = '$current_id', ";
		if (isset($story_left)) $str_params = $str_params."`story_left` = '$story_left', ";

		if (isset($mom)) $str_params = $str_params."`mom` = '$mom', ";
		if (isset($patience)) $str_params = $str_params."`patience` = '$patience', ";
		if (isset($insure)) $str_params = $str_params."`insure` = '$insure', ";
		if (isset($deport)) $str_params = $str_params."`deport` = '$deport', ";
		//if (isset($debt)) $str_params = $str_params."`debt` = '$debt', ";

		if (isset($work_msg)) $str_params = $str_params."`work_msg` = '$work_msg', ";
		if (isset($motiv_msg)) $str_params = $str_params."`motiv_msg` = '$motiv_msg', ";
		if (isset($room_msg)) $str_params = $str_params."`room_msg` = '$room_msg', ";
		if (isset($tax_msg)) $str_params = $str_params."`tax_msg` = '$tax_msg', ";
		if (isset($market_msg)) $str_params = $str_params."`market_msg` = '$market_msg', ";
		if (isset($insure_msg)) $str_params = $str_params."`insure_msg` = '$insure_msg', ";

		if (isset($week_msg)) $str_params = $str_params."`week_msg` = '$week_msg', ";
		if (isset($race_stat)) $str_params = $str_params."`race_stat` = '$race_stat', ";
		if (isset($note_request)) $str_params = $str_params."`note_request` = '$note_request', ";

		if (isset($scene)) $str_params = $str_params."`scene` = '$scene', ";

		$str_params = substr( $str_params, 0, strlen($str_params) - 2 );

		//тут бы ещё проверк на то, есть ли вообзе работа у пользователя (хотя Turns )
		if ( $mysqli->query("UPDATE `user_params` SET $str_params WHERE `id` = '$param_id';") == TRUE )
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully update user params info";
		}
	}
	// КОНЕЦ USER_PARAMS

	//ПАРАМЕТРЫ USER_WORK
	$emp_id = $_POST["emp_id"]; //Пока не используется, ибо у пользователя только одна работа
	$turns = $_POST["turns"];
	$worked = $_POST["worked"];
	$week = $_POST["week"];

	// ТАБЛИЦА USER_WORK ОБНОВЛЕНИЕ
	if ( (isset($turns) || isset($week) || isset($worked)) && !isset($emp_id) ) // Просто обновление ходов чаще всего
	{
		$str_work = "";
		if (isset($turns)) $str_work = $str_work."`turns` = '$turns', ";
		if (isset($week)) $str_work = $str_work."`week` = '$week', ";
		if (isset($worked)) $str_work = $str_work."`worked` = '$worked', ";

		$str_work = substr( $str_work, 0, strlen($str_work) - 2 );

		$employer = $_POST["employer"];
		//тут бы ещё проверк на то, есть ли вообзе работа у пользователя (хотя Turns )
		if ( $mysqli->query("UPDATE `user_work` SET $str_work  WHERE `user_id` = '$id' AND `emp_id` = '$employer';") == TRUE )
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully update work info";

			// ОБНОВЛЕНИЕ ДАННЫХ ПРОИЗВОДИТЕЛЬНОСТИ ГОСКОМПАНИЙ
			if (isset($employer))
			{
				if ($mysqli->query("UPDATE `employers` SET `produce` = `produce` + 1, `mining` = `mining` + 1 WHERE `id` = '$employer';"))
					$out["msg"] = $out["msg"]." update employer efficiency";
			}
		}
	}
	// ТАБЛИЦА USER_WORK СОЗДАНИЕ ЗАПИСИ
	if (isset($emp_id) && isset($week) && !isset($turns)) //Получается, когда задан emp_id - просто создаётся работа
	{
		$add_work = "INSERT INTO `user_work` (`id`, `user_id`, `emp_id`, `turns`,`worked`,`week`) VALUES (NULL, '$id', '$emp_id', '10','0','$week' );";
		if ( $mysqli->query($add_work) == TRUE )
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully add new work";
		}
	}
	//КОНЕЦ USER_WORK

	//ПАРАМЕТРЫ USER_OPER / BANK
	$balance = $_POST["balance"];
	$pay = $_POST["pay"];
	$oper_id = $_POST["oid"];
	$coins =$_POST["coins"];
	//ТАБЛИЦА USER_OPER (BANK)
	if (isset($oper_id))
	{
		$str_bank = "`balance` = '$balance'";
		if ( isset($pay) and isset($coins) ) $str_bank = $str_bank.", `pay` = '$pay', `coins` = '$coins'";

		if ($mysqli->query("UPDATE `user_oper` SET $str_bank  WHERE `user_id` = '$id' AND `oper_id` = '$oper_id' ;") == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully update bank info";
		}

	}
	//КОНЕЦ USER_OPER (BANK)

	//ПАРАМЕТРЫ USER_BONUS
	$bonus_id = $_POST["bid"];
	// ТАБЛИЦА USER_BONUSES
	if (isset($bonus_id))
	{ // Всегда тольео добавление, ибо бонусы отдельными задаются полями и добавить лишнего не даёт визуальный интерфейс
		$str_bon = "INSERT INTO `user_bonus` (`id`, `bonus_id`, `user_id`) VALUES (NULL, '$bonus_id', '$id');";
		if($mysqli->query($str_bon) == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully add user bonus";
		}
	}
	//КОНЕЦ BONUSES

	//ПАРАМЕТРЫ ДОБАВЛЕНИЯ USER_IP
	$owner = $_POST["owner"];
	$name = $_POST["name"];
	$adress = $_POST["adress"];
	$res_id = $_POST["res_id"];
	$weeks = $_POST["weeks"];
	//$capital = $_POST["capital"];

	// ТАБЛИЦА USER_IP
	if (isset($owner))
	{ // Всегда тольео добавление, ибо бонусы отдельными задаются полями и добавить лишнего не даёт визуальный интерфейс
		$str_ip = "INSERT INTO `user_ip` (`id`, `user_id`, `owner`, `name`, `adress`, `res_id`, `capital`,`tool_id`,`workers`,`mine_profit`,`weeks`,`mines`, `months`, `profit`, `total`)
			VALUES (NULL, '$id', '$owner', '$name', '$adress', '$res_id', '0', NULL,'0','0','$weeks','3','0','0','0');";
		if($mysqli->query($str_ip) == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully add ip status issue";

			//Информация об ip успешно добавлена
			if($res_ip = $mysqli->query("SELECT * FROM `user_ip` WHERE `user_id`='$id' ORDER BY `id` DESC"))
			{
				//id только что добавленной организации
				$row_ip = $res_ip->fetch_assoc();
				$ip_id = $row_ip["id"];

				$qwery = "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '1', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '1', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '1', '3', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '2', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '2', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '2', '3', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '3', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '3', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '3', '3', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '4', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '4', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '4', '3', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '5', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '5', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '5', '3', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '6', '1', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '6', '2', '0');";
				$qwery .= "INSERT INTO `ip_resources` (`id`, `ip_id`, `res_id`, `stage`, `amount`) VALUES (NULL, '$ip_id', '6', '3', '0');";

				$mysqli->multi_query($qwery);
			}

		}
	}

	// ОБНОВЛЕНИЕ ИНФОРМАЦИИ О ПРЕДПРИЯТИИ
	$ip_id = $_POST["ip_id"];
	if (isset ($ip_id))
	{

		// ОБНОВЛЕНИЕ ИНФОРМАЦИИ О РЕСУРСАХ В СУНДУКЕ
		$res_id = $_POST["res_id"];
		$stage = $_POST["stage"];
		$amount = $_POST["amount"];

		if (( isset($stage) )&&( isset($res_id) )&&( isset($amount) ))
		{
			$mysqli->query("UPDATE `ip_resources` SET `amount` = `amount` + $amount WHERE `ip_id` = '$ip_id' AND `res_id` = '$res_id' AND `stage` = '$stage';");

			//Обновление доходности компании (для налогов)
			$profit = $_POST["profit"];
			$total = $_POST["total"];
			if ( isset($profit) || isset($total) )
			{
				$str_profit = "";
				if (isset($profit)) $str_profit = $str_profit."`profit` = `profit`+ '$profit', ";
				if (isset($total)) $str_profit = $str_profit."`total` = `total` + '$total', ";
				$str_profit = substr( $str_profit, 0, strlen($str_profit) - 2 );

				$mysqli->query("UPDATE `user_ip` SET $str_profit WHERE `id` = '$ip_id';");

				$current_mine = $_POST["current_mine"];
				if (isset($current_mine))
					$mysqli->query("UPDATE `user_ip` SET `current_mine` = $current_mine WHERE `id` = '$ip_id';");
			}
			$out["msg"] = $out["msg"]." Successfully update user_ip info";
		}

		// ИЗМЕНЕНИЕ УРОВНЯ СТАНКА
		$tool_id = $_POST["tool_id"];
		//Стоимость каждого ресурса
		$amount = $_POST["amount"];
		$stage = $_POST["stage"];

		if ( isset($tool_id) && isset($amount) && isset($stage) )
		{
			if ( $mysqli->query("UPDATE `ip_resources` SET `amount` = `amount` - $amount WHERE `ip_id` = '$ip_id' AND `stage` = '$stage' AND `res_id` <> 6;") == TRUE )
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully update tool lvl issue";
				$mysqli->query("UPDATE `user_ip` SET `tool_id` = '$tool_id' WHERE `id` = '$ip_id';");
			}
		}

		// ЛОГИКА НАЙМА РАБОЧИХ
		$workers = $_POST["workers"];
		$worker_id = $_POST["worker_id"];

		if ( isset($workers) && isset($worker_id) )
		{
			//Внутри ещё одна проверка, чтобы не нанять больше рабочих, чем шахт
			if ( $mysqli->query("UPDATE `user_ip` SET `workers` = `workers` + $workers WHERE `id` = '$ip_id';") == TRUE )
			{
				if ($workers > 0) //найм
				{
					$mysqli->query("INSERT INTO `ip_workers` (`id`, `user_id`, `ip_id`, `turns`, `motivation`, `work_profit`, `work_total`) VALUES (NULL, '$worker_id', '$ip_id', '10', '1000', '0', '0');");
					$out["flag"] = 1;
					$out["msg"] = $out["msg"]." Successfully hire worker";
				}
				else //увольнение
				{
					$mysqli->query("DELETE FROM `ip_workers` WHERE `user_id` = '$worker_id' AND `ip_id` = '$ip_id';");
					$out["flag"] = 1;
					$out["msg"] = $out["msg"]." Successfully dismiss worker";
				}
			}
		}

		// УВЕЛИЧЕНИЕ ЧИСЛА ШАХТ
		$mines = $_POST["mines"];
		if ( isset($mines))
		{
			if ( $mysqli->query("UPDATE `user_ip` SET `mines` = $mines WHERE `id` = '$ip_id';") == TRUE )
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully update mine count";
			}
		}

		// ВЫАЛАТА З/П
		$paid = $_POST["paid"];
		if ( isset($paid))
		{
			//У пользователя изменения coins - ниже
			if ( $mysqli->query("UPDATE `user_ip` SET `paid` = $paid WHERE `id` = '$ip_id';") == TRUE )
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully paid salary count";
			}
		}

	}
	//КОНЕЦ IP

	//ПАРАМЕТРЫ USER_APART
	$apart_id = $_POST["apart_id"];
	$atmosphere = $_POST["atmosphere"];
	// ТАБЛИЦА USER_APART
	if ( (isset($apart_id))&& (!isset($atmosphere)) )
	{ // Всегда тольео добавление, ибо бонусы отдельными задаются полями и добавить лишнего не даёт визуальный интерфейс
		$str_apart = "INSERT INTO `user_apart` (`id`, `apart_id`, `user_id`) VALUES (NULL, '$apart_id', '$id');";
		if($mysqli->query($str_apart) == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully add user araptment";
		}
	}
	if ( isset($apart_id) && isset($atmosphere) )
	{ // Обновить данные атмосферы в квартире
		$str_apart = "UPDATE `user_apart` SET `atmosphere` = '$atmosphere' WHERE `user_id` = '$id' AND `apart_id` = '$apart_id';";
		if($mysqli->query($str_apart) == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully update araptment info";
		}
	}
	//КОНЕЦ USER_APART

	//ПАРАМЕТРЫ USER_STOCK
	$item_id = $_POST["item_id"];
	$space = $_POST["space"];
	$is_active = $_POST["is_active"];
	$is_new = $_POST["is_new"];
	$left_days = $_POST["left_days"];
	// ТАБЛИЦА USER_STOCK
	if (isset($item_id))
	{
		if ( (!isset($space)) && (!isset($is_active)) && (!isset($is_new)) && (!isset($left_days)) )
		{ //Покупка предмета - добавление в таблицу предметов пользователя
			$mysqli->query("INSERT INTO `user_stock` (`id`, `item_id`, `space`, `user_id`, `is_active`, `is_new`) VALUES (NULL, '$item_id', '1', '$id', '0', '1');");
		}
		else
		{ //Обработка обновления статуса предмета
			$str_stock = "";
			// Сделать частный случай активации бонуса, удаляется запись о нём
			if (isset($space)) $str_stock = $str_stock."`space` = '$space', ";
			if (isset($is_active)) $str_stock = $str_stock."`is_active` = '$is_active', ";
			if (isset($is_new)) $str_stock = $str_stock."`is_new` = '$is_new', ";
			if (isset($left_days)) $str_stock = $str_stock."`left_days` = '$left_days', ";
			$str_stock = substr( $str_stock, 0, strlen($str_stock) - 2 );

			if($mysqli->query("UPDATE `user_stock` SET $str_stock WHERE `user_id` = '$id' AND `id` = '$item_id';") == TRUE)
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully update user stock info";
			}

		}
	}
	//КОНЕЦ USER_STOCK

	//ПАРАМЕТРЫ ACHIEVES (ОБЩИЙ СЛУЧАЙ УЧЁБЫ)
	$achieve_id = $_POST["achieve_id"];
	$left = $_POST["left"];
	$complete = $_POST["complete"];
	$prize = $_POST["prize"];
	//ТАБЛИЦА USER_ACHIEVES

	if (isset($achieve_id))
	{
		$res_achieve = $mysqli->query("SELECT * FROM `user_achieve` WHERE `user_id`='$id' AND `achieve_id` = '$achieve_id' ");
		$row["achieve"] = array();
		if ($row_achieve = $res_achieve->fetch_assoc()) //уже существует такая ачивка
		{ //Так как существует, пытаемся обносить её данные
			$str_achieve = "";
			if ( isset($complete) ) $str_achieve = $str_achieve."`complete` = '$complete', ";
			if ( isset($prize) ) $str_achieve = $str_achieve."`prize` = '$prize', ";
			if ( isset($left) ) $str_achieve = $str_achieve."`left` = '$left', ";
			$str_achieve = substr($str_achieve,0,strlen($str_achieve)-2);

			if ($mysqli->query("UPDATE `user_achieve` SET $str_achieve  WHERE `user_id` = '$id' AND `achieve_id` = '$achieve_id' ;") == TRUE)
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully update achieve info";
			}
		}
		else
		{ // Если не существует - добавляем
			if ( !isset($left) ) $left=21 ; //Если не задан left при создании - присваивается стандартное значение, можно через базу
			if ( !isset($complete) ) $complete=0 ;
			if ( !isset($prize) ) $prize=0 ;

			$str_achieve = "INSERT INTO `user_achieve` (`id`, `achieve_id`, `user_id`, `left`, `complete`, `prize`) VALUES (NULL, '$achieve_id', '$id', '$left', '$complete','$prize');";
			if($mysqli->query($str_achieve) == TRUE)
			{
				$out["flag"] = 1;
				$out["msg"] = $out["msg"]." Successfully add user achieve id: ".$achieve_id;
			}
		}
	}
	//КОНЕЦ ACHIEVE

	//ПАРАМЕТРЫ USER JOURNAL
	$journal_id = $_POST["journal_id"];
	// ТАБЛИЦА JOURNAL
	if (isset($journal_id))
	{
		$show = $_POST["show"];
		$read = $_POST["read"];
		if (!isset($show)) $show = 1;
		if (!isset($read)) $read = 1;

		if ($mysqli->query("UPDATE `journal` SET `show` = '$show', `read` = '$read', `updated` = CURRENT_TIMESTAMP WHERE `user_id` = '$id' AND `id` = '$journal_id' ;") == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = $out["msg"]." Successfully update journal entry";
		}
	}
	//КОНЕЦ JOURNAL

	//ПАРАМЕТРЫ USERS
	$name = $_POST["name"];
	$ava = $_POST["ava"];

	$status = $_POST["status"];
	$status_id = $_POST["status_id"];
	$lvl = $_POST["lvl"];
	$update_lvl = $_POST["update_lvl"];

	$magic = $_POST["magic"];
	$gender = $_POST["gender"];
	$appear = $_POST["appear"];

	$exp = $_POST["exp"];
	$motiv = $_POST["motiv"];
	$joy = $_POST["joy"];
	$health = $_POST["health"];
	$coins = $_POST["coins"];
	$cpoint = $_POST["cpoint"];
	$points = $_POST["points"];

	$bday = $_POST["bday"];

	$pquest = $_POST["pquest"];
	$gquest = $_POST["gquest"];

	//Может, нужна будет общая проверка для искючения левых изменений при пересечении значений параметров
	//(!isset($oper_id))&&(!isset($achieve_id))&&(!isset($emp_id))&&(!isset($bonus_id)) {}
	//ТАБЛИЦА USERS
	$str_user = "";
	// Про owner - чтобы исключить изменения при создании ip
	if (isset($name) and !isset($owner) ) $str_user = $str_user."`name` = '$name', ";
	if (isset($ava)) $str_user = $str_user."`ava` = '$ava', ";

	if (isset($status)) $str_user = $str_user."`status` = '$status', ";
	if (isset($status_id)) $str_user = $str_user."`status_id` = '$status_id', ";
	if (isset($lvl)) $str_user = $str_user."`lvl` = '$lvl', ";
	if (isset($update_lvl)) $str_user = $str_user."`update_lvl` = '$update_lvl', ";
	if (isset($magic)) $str_user = $str_user."`magic` = '$magic', ";
	if (isset($gender)) $str_user = $str_user."`gender` = '$gender', ";
	if (isset($appear)) $str_user = $str_user."`appear` = '$appear', ";

	if (isset($joy)) $str_user = $str_user."`joy` = '$joy', ";
	if (isset($health)) $str_user = $str_user."`health` = '$health', ";
	if (isset($exp)) $str_user = $str_user."`exp` = '$exp', ";
	if (isset($motiv)) $str_user = $str_user."`motiv` = '$motiv', ";
	if (isset($points)) $str_user = $str_user."`points` = '$points', ";
	// Дата рождения
	if (isset($bday)) $str_user = $str_user."`bday` = '$bday', ";

	if (isset($pquest)) $str_user = $str_user."`pquest` = '$pquest', ";
	if (isset($gquest)) $str_user = $str_user."`gquest` = '$gquest', ";

	//Работает через прибавление (условие на oper_id для того, чтобы исключить использование coins при обновлении банка банке )
	if ( isset($coins) && !isset($oper_id) )
	{
		$str_user = $str_user."`coins` = `coins` + '$coins', ";

		$transfer = $_POST["transfer"];
		if (!isset($transfer) && ($coins > 0)) $str_user = $str_user."`race_profit` = `race_profit` + '$coins', ";
	}
	if ( isset($cpoint) ) $str_user = $str_user."`cpoint` = `cpoint` + '$cpoint', ";
	//Тут нужна проверка на то, что строка не пустая, чтобы скрипт выполнять только если задано что-то для изменения в пользователе
	$str_user = substr($str_user,0,strlen($str_user)-2); //Удаление последней запятой и пробела
	$qwery = "UPDATE `users` SET $str_user WHERE `id` = '$id';";
	if ($mysqli->query($qwery) == TRUE)
	{
		$out["flag"] = 1;
		$out["msg"] = $out["msg"]." Successfully update user info";
	}
	//КОНЕЦ USERS
}
else
	$out["msg"] = "ID isn't set!";

//Возврат резултата
echo json_encode($out);

?>
