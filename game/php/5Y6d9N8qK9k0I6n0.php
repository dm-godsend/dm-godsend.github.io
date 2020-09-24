<?php
	// header('Access-Control-Allow-Origin: *');
	// header('Access-Control-Allow-Headers: *');
	// header('Access-Control-Allow-Credentials: true');
	function timestampToLocal($sql, $formatDate)
	{
		$time_res = $sql->query("SELECT * FROM `time`");
		$time_loc = $time_res->fetch_assoc();

		$day = $time_loc["day"]+1;
		$month = $time_loc["month"];
		$year = $time_loc["year"]+1;

		if (!isset($formatDate))
		   $tm = time();
		else
		   $tm = strtotime($formatDate);

		$nowdt = mktime( 0,0,0,date( "m",$tm ),date( "d",$tm ),date("y",$tm ) );
		$time_global = ($tm - $nowdt);
		$day_week = intdiv($time_global, 12300);
		$day_part = ($time_global % 12300)/12300;

		$time_local = $day_part * 86400;
		$loc_hour = intdiv($time_local, 3600);
		$loc_min = ($time_local % 3600)/3600;

		$loc_min = intval($loc_min*60);
		// local sec
		// min, sec = math.modf(min * 60)
		// sec = sec * 60

		$loc_hour = ($loc_hour < 10)? "0".$loc_hour : $loc_hour;
		$loc_min = ($loc_min < 10)? "0".$loc_min : $loc_min;

		if ($day_week == 0)
	      $day_week = 'Понедельник';
	   elseif($day_week == 1)
	      $day_week = 'Вторник';
	   elseif($day_week == 2)
	      $day_week = 'Среда';
	   elseif($day_week == 3)
	      $day_week = 'Четверг';
	   elseif($day_week == 4)
	      $day_week = 'Пятница';
	   elseif($day_week == 5)
	      $day_week = 'Суббота';
	   elseif($day_week == 6)
	      $day_week = 'Воскресенье';
	   else
	      $day_week = 'Понедельник';

	   if($month == 0)
	      $month = 'Января';
	   elseif ($month == 1)
	      $month = 'Февраля';
	   elseif ($month == 2)
	      $month = 'Марта';
	   elseif ($month == 3)
	      $month = 'Апреля';
	   elseif ($month == 4)
	      $month = 'Мая';
	   elseif ($month == 5)
	      $month = 'Июня';
	   elseif ($month == 6)
	      $month = 'Июля';
	   elseif ($month == 7)
	      $month = 'Августа';
	   elseif ($month == 8)
	      $month = 'Сентября';
	   elseif ($month == 9)
	      $month = 'Октября';
	   elseif ($month == 10)
	      $month = 'Ноября';
	   elseif ($month == 11)
	      $month = 'Декабря';
	   else
	      $month = 'Января';

		$dm = (!isset($formatDate)) ? $day." ".$month.", " : "";
		return $day_week.", ".$dm.$loc_hour.":".$loc_min;
	}

	$sr = "sql7.freemysqlhosting.net"; //или "localhost" - нужно тестировать в реале
	$un = "sql7367136";
	$pw = "DyIkunK7Kt";
	$db = "sql7367136";

	if (true)
	{
		$out["flag"] = 0;
		$out["msg"] = "Server unreachable";

		$mysqli = new mysqli($sr, $un, $pw, $db);//может, вынести подключение в отдельный php
		if ($mysqli->connect_errno)
		{
			$out["msg"] = "Connect error";
			exit;
		}
		else
		{
			$mysqli->set_charset ("utf8");
			$out["msg"] = "Only connect to db";

			$tm = time();
			$nowdt = mktime( 0,0,0,date( "m",$tm ),date( "d",$tm ),date("y",$tm ) );
			$start_day = ($tm - $nowdt); //%(3600*24)+date("Z"); // кол-во секунд с начала дня
			$out["time"] = $start_day;

			//НАЧАЛО ОСНОВНОГО NEW_DAY (НАЧАЛО ИГРОВОЙ НЕДЕЛИ)

			//скрипт запускается в самом начале реального дня, т.е начало недели в игре
			$mysqli->query("UPDATE `time` SET `isMagic` = 0, `wday` = 0, `week` = `week` + 1, `updated_at` = CURRENT_TIMESTAMP");
			//Обновление флагов всех сообщений и временных параметров
			$mysqli->query("UPDATE `user_params` SET `treasure` = 1, `employ` = 0, `work_msg` = 1, `room_msg` = 1, `tax_msg` = 1, `insure_msg` = 1, `market_msg` = 1, `race_stat` = 1");
			//Конец инфляции в начале нового дня (возможно, организовать таблицу user_events с временными эффектами и их параметрами, а не хранить всё в params)
			//$mysqli->query("UPDATE `user_params` SET `story_id` = `story_id`+ 1 WHERE `story_id`=38");

			$mysqli->query("UPDATE `user_ip` SET `weeks` = `weeks` - 1  WHERE `weeks` > 0 ;");
			$mysqli->query("UPDATE `user_work` SET `turns` = 10");

			//Награда за учёбу
			$mysqli->query("UPDATE `user_achieve` SET `prize` = 1 WHERE `complete` = 2 AND `achieve_id` = 1");

			//Статус страховки (чтобы не менял при действующей страховке)
			$mysqli->query("UPDATE `user_params` SET `race_stat`= 1 WHERE `race_stat` = 0");

			//ПРИСВАИВАНИЕ СТАТУСА В ГОНКЕ НА НАЧАЛО НЕДЕЛИ ПО РЕЙТИНГУ
			//стандартно просто новое сообщение о начале гонки
			$mysqli->query("UPDATE `user_params` SET `race_stat`=1");
			$qwery_race2 = "UPDATE `user_params` SET `race_stat`=2 WHERE `user_id` IN
			   (
			       SELECT * FROM (SELECT `id` FROM `users` WHERE `status_id`='2' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 100 ) a
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='1' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 100 ) b
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='3' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 100 ) c
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='4' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 100 ) d
			   )";
			$qwery_race3 = "UPDATE `user_params` SET `race_stat`=3 WHERE `user_id` IN
			   (
			       SELECT * FROM (SELECT `id` FROM `users` WHERE `status_id`='2' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 10 ) a
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='1' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 10 ) b
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='3' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 10 ) c
			       UNION select * from (SELECT `id` FROM `users` WHERE `status_id`='4' AND `race_profit`>0 ORDER BY `race_profit` DESC LIMIT 10 ) d
			   )";
			$mysqli->query($qwery_race2);
			$mysqli->query($qwery_race3);

			$mysqli->query("UPDATE `users` SET `race_profit` = 0 WHERE `id`>1");
			//КОНЕЦ ПОДСЧЁТА РЕЙТИНГА В ГОНКЕ

			//Здесь хранятся все глобальные параметры
			$global_res = $mysqli->query("SELECT * FROM `time`");
			$gtime = $global_res->fetch_assoc();
			$roomCost = $gtime["room_cost"];

			$res_users = $mysqli->query("SELECT * FROM `users` WHERE `magic` = 1");
			//SELECT * FROM `users` WHERE `id` IN (select `user_id` from `user_apart`)
			//Чтобы вообще не использовать magic
			// ВЫЧИТАНИЕ АРЕНДНОЙ ПЛАТЫ ПО КВАРТИРЕ, НАЧИСЛЕНИЕ ДОЛГОВ
			while ($row_user = $res_users->fetch_assoc())
			{
				$user_id = $row_user["id"];
				$coins = $row_user["coins"];

				$debtCoins = $roomCost - $coins;
				$local_date = timestampToLocal($mysqli);
				//Тут учитывать уже существующую задолжность
				if ($coins >= $roomCost)
				{
					// Хватает денег
					$mysqli->query("UPDATE `users` SET `coins` = `coins` - $roomCost  WHERE `id` = '$user_id'");
					//Вызывается с 1-й, значит будет показывать на сцене
					// $mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
					// 	VALUES (NULL, '$user_id', 'Счёт за квартиру оплачен', '0', '1', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
				}
				else //недостаточно средств
				{
					//Занесение информации об оставшемся долге за квартиру
					$mysqli->query("UPDATE `user_apart` SET `debt` = $debtCoins WHERE `id` = '$user_id'");
					if ($coins > 0) $mysqli->query("UPDATE `users` SET `coins` = 0  WHERE `id` = '$user_id'");

					$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
						VALUES (NULL, '$user_id', 'Будьте добросовестным арендатором! У вас остался долг за квартиру $debtCoins рублей', '0', '1', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
				}
			}
			// КОНЕЦ АРЕНДЫ КВАРТИРЫ

			// ОБНОВЛЕНИЕ ПОКАЗАТЕЛЕЙ ПРОИЗВОДИТЕЛЬНОСТИ ГОС КОМПАНИЙ ЗА НЕДЕЛЮ
			$res_emp = $mysqli->query("SELECT * FROM `employers`");
			while ($row_emp = $res_emp->fetch_assoc())
			{
				$emp_id = $row_emp["id"];
				//Заполнение массива сундука данными
				$res_worker = $mysqli->query("SELECT count(*) as 'cnt' FROM `user_work` WHERE `emp_id`='$emp_id'");
				//Загружается только одно предприятие, потом можно поменять на while
				if ($row_worker = $res_worker->fetch_assoc())
					$cnt = $row_worker["cnt"];
				$cnt = ($cnt == 0) ? 1 : $cnt;

				$eff = $row_emp["produce"] / $cnt / 10; //т.к 10 - норма для одного сотрудника

				$mysqli->query("UPDATE `employers` SET `efficiency` = $eff, `produce` = 0  WHERE `id` = '$emp_id'");
			}
			// КОНЕЦ РАССЧЕТА ПРОИЗВОДИТЕЛЬНОСТИ

			for ($i = 1; $i <= 7; $i++)
			{
				$mysqli->query("UPDATE `time` SET `isMagic` = 0, `day_updated` = CURRENT_TIMESTAMP");
				//Изменение всех сюжетных таймеров на 1
				$mysqli->query("UPDATE `user_params` SET `story_left` = `story_left` - 1 WHERE `story_left` > 0");
				//Начало игрового дня
				$mysqli->query("UPDATE `user_work` SET `turns` = 10 WHERE `emp_id` = 6"); //обновление смен на Лесопиоке каждый день
				//$mysqli->query("UPDATE `users` SET `joy` = 1 ");
				//Аренда квартиры (пока magic - для удобсва флаг наличия квартиры)
				//$mysqli->query("UPDATE `users` SET `coins` = `coins` - 500 WHERE `magic` = 1");
				//в magic - наличие квартиры для удобства

				// Изменение оставшегося времени учёбы и прошедших дней кредита/депозита
				$mysqli->query("UPDATE `user_oper` SET `left` = `left` + 1");
				$mysqli->query("UPDATE `study` SET `left` = `left` - 1");

				$mysqli->query("UPDATE `user_achieve` SET `left` = `left` - 1 WHERE `complete` = 0 AND `left` > 0;");
				//Возможно не сработает, если долго будет предыдущий update, тогда update выполнять только до 1-го, а потом уже отлавливать
				$mysqli->query("UPDATE `user_achieve` SET `complete` = 1 WHERE `left` = 0 AND `complete` = 0;");

				//Увеличесни срока в днях с начала месяца выставления налогов
				$mysqli->query("UPDATE `ip_tax` SET `left` = `left` + 1");

				//Уменьшение срока действия эффектов от бонусов на день
				$mysqli->query("UPDATE `user_stock` SET `left_days` = `left_days`- 1 WHERE `left_days`>0 AND `is_active` = 2 ");
				$mysqli->query("DELETE FROM `user_stock` WHERE `left_days` = 0 AND `is_active` = 2;");

				//Начисление штрафов за неотработку смен (считать стаж)
				// ГЕНЕРАЦИЯ РАБОТЫ БОТОВ + АВТОСНЯТИЕ ДОЛГОВ ПО НАЛОГАМ (Учёт учловия - после первой недели - в конце)
				if ($i == 1) //ПЕРВЫЙ ДЕНЬ НЕДЕЛИ
				{
					// РАБОТА БОТОВ НА ПРЕДПРИЯТИЯХ ЗА НЕДЕЛЮ / ПРИБЫТЬ ОТ РАБОЧИХ / АВТОШТРАФ
					if ($res = $mysqli->query("SELECT * FROM `user_ip`;"))
					{
						while ($row = $res->fetch_assoc()) // все действующие организации
						{
							$user_id = $row["user_id"];
							$tool_id = $row["tool_id"];
							$ip_id = $row["id"];
							$res_id = $row["res_id"];
							if (!isset($tool_id)) $tool_id = 0;

							//Данные о владельце
							$res_user = $mysqli->query("SELECT * FROM `users` WHERE `id` = '$user_id';");
							$row_user = $res_user->fetch_assoc();
							$status = $row_user["status"];
							$status_id = $row_user["status_id"];

							//Получение списка навыков (чтобы учесть бонусы понижающие налоги и увеличивающие выработку)
							$res_ability = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id` = '$user_id';");
							$abil = array();
							while ($row_ability = $res_ability->fetch_assoc())
								array_push($abil, $row_ability);

							//По идее лучше через LEFT JOIN ЗАБРАТЬ и сразу использовать нужные данные
							$stage = 1;
							if ($tool_id == 0)
								$stageCost = 150;
							else if (($tool_id > 0) && ($tool_id<=6))
							{
								$stage = 2;
								$stageCost = 2400;
							}
							else
							{
								$stage = 3;
								$stageCost = 38400;
							}

							//СИМУЛЯЦИЯ РАБОТЫ БОТОВ
							$workers = $row["workers"];

							$res_worker = $mysqli->query("SELECT * FROM `ip_workers` WHERE `ip_id` = '$ip_id';");
							while ($row_worker = $res_worker->fetch_assoc())
							{
								$motiv = $row_worker["motivation"];
								//if ($motiv >= 250) $motiv = 1000;
								$motiv/=1000;
								$turns = ceil($motiv*10);

								for ($j = 1; $j <= $turns; $j++)
								{
									//c учётом навыка вероятность успеха (удача)
									$sucess = rand(0, 100 + $abil[0]["value"]*100);
									$sucess = intdiv($sucess, 20) + 1;
									if ($sucess >= 6) $sucess = 5;

									//с учётом навыка (+ материал за партию) (скорость)
									$count = $sucess + ceil($abil[9]["value"]);
									//Чтобы разделить то, что получаем благодаря скилу и без него (налоги и з/п только за читую добычу)
									$profit = $sucess*$stageCost;
									$total = $count*$stageCost;

									$mysqli->query("UPDATE `user_ip` SET `profit` = `profit` + '$profit', `mine_profit` = `mine_profit` + '$profit', `total` = `total` + $total WHERE `id` = '$ip_id';");
									$mysqli->query("UPDATE `ip_resources` SET `amount` = `amount` + '$count' WHERE `ip_id` = '$ip_id' AND `res_id` = '$res_id' AND `stage` = '$stage';");
								}
							}

							//ОБРАБОТКА АВТОШТРАФА ПО НАЛОГАМ ПРИ ЗАДОЛЖНОСТИ
							// $taxProfit = 0;
							// //не забыть учесть вероятность
							// //По дее лучше делать LEFT JOIN и брать значения напрямую
							// if ($status == "ИП")
							// 	$taxProfit = 0.06;
							// else if ($status == "ООО")
							// 	$taxProfit = 0.1;
							// else if ($status == "ПАО")
							// 	$taxProfit = 0.15;
							//
							// //Учёт бонуса скилов, понижающих доходный налог и все налоги
							// $taxProfit -= $abil[2]["value"] - $abil[4]["value"];
							//
							// // Для ботов потом ввести ограничения на размер ЗП
							// $salaryRate = 0.4;
							// $taxSalary = 0.423; //глобальным сделать + вероятность
							//
							// $rdate = $mysqli->query("SELECT * FROM `time`");
							// $time = $rdate->fetch_assoc();
							// //Пени
							// $salaryFine = 0.0725;
							// $profitFine = $time["rate"]; //ставка рефинансирования
							//
							// //Учёт бонуса скилов, понижающих налог на рабочих и все налоги
							// //Может, не совсем корректно делает это. По идее должен от итоговой суммы вычислять процент
							// $taxSalary -= $abil[3]["value"] - $abil[4]["value"];
							//
							// $tool_id = $row["tool_id"];
							// if (!isset($tool_id)) $tool_id = 0;
							//
							// $taxProfit*=$row["profit"];
							//
							// $res_tax = $mysqli->query("SELECT * FROM `ip_tax` WHERE `ip_id` = '$ip_id';");
							// $taxes = array();
							// while ($row_tax = $res_tax->fetch_assoc())
							// 	array_push($taxes, $row_tax);
							//
							// if (count($taxes)>0)
							// {
							// 	//Где-то глобально задать число дней // Если первый день недели, а налог уже висит 5 дней
							// 	//Значит это уже начало второй недели и надо штрафовать
							// 	//if ($taxes[0]["left"] > 55) -- три месяца
							// 	if ($taxes[0]["left"] > 5)
							// 	{
							// 		//срок истёк, АВТОСНЯТИЕ ШТРАФА И ПЕНИ
							// 		$totalPay = 0;
							// 		$profitTax = $taxes[0]["sum"];
							// 		$profitFine*=$profitTax;
							//
							// 		$totalPay += $profitTax + $profitFine;
							//
							// 		if (count($taxes)>1)
							// 		{
							// 			$salaryTax = $taxes[1]["sum"];
							// 			$salaryFine*=$salaryTax;
							//
							// 			$totalPay += $salaryTax + $salaryFine;
							// 		}
							//
							// 		//В случае лешения статуса ИП
							// 		// $mysqli->query("DELETE FROM `ip_resources` WHERE `ip_id` = '$ip_id';");
							// 		// $mysqli->query("DELETE FROM `ip_workers` WHERE `ip_id` = '$ip_id';");
							// 		// $mysqli->query("DELETE FROM `market` WHERE `ip_id` = '$ip_id';");
							// 		// $mysqli->query("UPDATE `users` SET `status` = 'Физическое лицо' WHERE `id` = '$user_id';"
							// 		// $mysqli->query("UPDATE `user_params` SET `deport` = '1' WHERE `user_id` = '$user_id';"
							//
							// 		//УДАЛЕНИЕ ЗАПИСИ О НАЛОГАХ
							// 		//Вычитаем налоги + пени, если всё успешно, удаляем записи о налогах пользователя
							// 		if ($mysqli->query("UPDATE `users` SET `coins` = `coins`- $totalPay WHERE `id` = '$user_id';") == TRUE)
							// 			$mysqli->query("DELETE FROM `ip_tax` WHERE `ip_id`='$ip_id';");
							//
							// 	}
							// }

						}
					}
				}
				// КОНЕЦ ОБРАБОТКИ АВТОШТРАФА / СИМУЛЯЦИИ РАБОЧИХ И ЗАРПЛАТЫ

				sleep(12300);

				//В КОНЦЕ ДНЯ
				$mysqli->query("UPDATE `time` SET `day` = `day` + 1, `wday` = `wday` + 1");
				//Условие на конец месяца
				$rdate = $mysqli->query("SELECT * FROM `time`");
				$time = $rdate->fetch_assoc();

				//КОНЕЦ МЕСЯЦА
				if ($time["day"] == 28)
				{
					//Начисление за преподавательскую деятельность, запись в журнал?
					$salaryAmount = 10000;
					$mysqli->query("UPDATE `users` SET `coins` = `coins`+'$salaryAmount' WHERE `id` IN (SELECT `user_id` FROM `user_params` WHERE `school` = 1)");
					// $mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`)
					// 	VALUES (NULL, '$user_id', 'Счёт за квартиру оплачен', '0', '1', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);");

					//Уменьшение атмосферы в квартире
					//$mysqli->query("UPDATE `user_apart` SET `atmosphere` = `atmosphere`-1 WHERE `atmosphere` > 0");

					//Появление бабки на карте вновь
					$mysqli->query("UPDATE `user_params` SET `grandma` = 1");
					//Увеличение счетчика месяцов, проверка нового года и пр
					$mysqli->query("UPDATE `time` SET `day` = 0, `month` = `month` + 1");
					$rdate = $mysqli->query("SELECT * FROM `time`");
					$time = $rdate->fetch_assoc();
					if ($time["month"] == 12) // конец года
						$mysqli->query("UPDATE `time` SET `month` = 0, `week` = 0, `year` = `year` + 1");

					//ДОХОДНОСТЬ И НАЛОГИ С ИП (СОЗДАНИЕ ЗАПИСЕЙ О НАЛОГАХ)
					if ($res = $mysqli->query("SELECT * FROM `user_ip`;"))
					{
						while ($row = $res->fetch_assoc()) // все действующие организации
						{
							//Пропускать выполнение для предприяьтй без прибыли в месяц
							if ($row["profit"] == 0) continue;

							// Для каждого ИП в базе
							$user_id = $row["user_id"];
							$ip_id = $row["id"];

							//Данные о владельце
							$res_user = $mysqli->query("SELECT * FROM `users` WHERE `id` = '$user_id';");
							$row_user = $res_user->fetch_assoc();
							$status = $row_user["status"];
							$status_id = $row_user["status_id"];

							//Получение списка навыков (чтобы учесть бонусы понижающие налоги)
							$res_ability = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id` = '$user_id';");
							$abil = array();
							while ($row_ability = $res_ability->fetch_assoc())
								array_push($abil, $row_ability);

							$taxProfit = 0;
							//не забыть учесть вероятность
							//По дее лучше делать LEFT JOIN и брать значения напрямую
							if ($status_id == 2)
								$taxProfit = 0.06;
							else if ($status_id == 3)
								$taxProfit = 0.1;
							else if ($status_id == 4)
								$taxProfit = 0.15;

							//Учёт бонуса скилов, понижающих доходный налог и все налоги
							$taxProfit -= $abil[2]["value"] - $abil[4]["value"];

							// Для ботов птом ввести ограничения на размер ЗП
							$salaryRate = 0.4;
							$taxSalary = 0.423; //глобальным сделать + вероятность

							$rdate = $mysqli->query("SELECT * FROM `time`");
							$time = $rdate->fetch_assoc();
							//Пени
							$salaryFine = 0.0725;
							$profitFine = $time["rate"];

							//Учёт бонуса скилов, понижающих налог на рабочих и все налоги
							$taxSalary -= $abil[3]["value"] - $abil[4]["value"];

							$tool_id = $row["tool_id"];
							if (!isset($tool_id)) $tool_id = 0;

							//Тоже лучше брать LEFT JOIN и напрямую забирать стоимость стадии из tool_id
							if ($tool_id == 0)
								$stageCost = 150;
							else if (($tool_id > 0) && ($tool_id<=6))
								$stageCost = 2400;
							else
								$stageCost = 38400;

							$taxProfit*=$row["profit"];

							$res_tax = $mysqli->query("SELECT * FROM `ip_tax` WHERE `ip_id` = '$ip_id';");
							$taxes = array();
							while ($row_tax = $res_tax->fetch_assoc())
								array_push($taxes, $row_tax);

							if (count($taxes)>0)
							{
								//В КОНЦЕ МЕСЯЦА НЕ МОЖЕТ БЫТЬ УЖЕ НЕОПЛАЧЕННЫХ НАЛОГОВ, АВТОСНЯТИЕ ПОСЛЕ 1-й недели
								if ($row["mine_profit"] > 0) // такое условие - чтобы не читерить увольнением в конце месяца
								{
									$salary = $salaryRate*$row["mine_profit"];
									$taxSalary*=$salary;
								}
								//Для всех текущих налогов увеличение суммы налога
								while ($row_tax = $res_tax->fetch_assoc())
								{
									$tax_id = $row_tax["id"];
									//Чтобы универсализировать первые два налога (доход - зп)
									$taxSumm = ($row_tax["tax_id"] == 1) ? $taxProfit : $taxSalary;
									$mysqli->query("UPDATE `ip_tax` SET `sum` = `sum` + '$taxSumm' WHERE `ip_tax`.`id` = $tax_id;");
								}
							}
							else
							{
								//Добавление налогов, если есть рабочие вообще + списание з/п и штраф при невозможноси платить
								if ($row["mine_profit"] > 0) // такое условие - чтобы не читерить увольнением в конце месяца
								{
									//Вся логика сотрудников внутри (штрафы / зп, мотивация)
									$salary = $salaryRate*$row["mine_profit"];
									//Налог - процент от зарплаты
									$taxSalary*=$salary;

									$mysqli->query("INSERT INTO `ip_tax` (`id`, `ip_id`, `tax_id`, `left`, `sum`) VALUES (NULL, '$ip_id', '1', '0', '$taxProfit'), (NULL, '$ip_id', '2', '0', '$taxSalary');");
									$local_date = timestampToLocal($mysqli);

									// АВТОВЫЧИТАНИЕ ЗАРПЛАТЫ ИЛИ ПОТЕРЯ РАБОЧЕГО, СОБЫТИЯ В ЖУРНАЛ
									if ( ($row["paid"] == 0) && ($salary <= $row_user["coins"]) )
									{
										//$fineSalary*=$row["mine_profit"];
										//Автовычитание з/п со штрафом, если до сих пор не заплатил (хранится )
										$mysqli->query("UPDATE `users` SET `coins` = `coins`- $salary WHERE `id` = '$user_id';");

										//Генерация события в журнал, в журнале отметка, создаёт ли это событие сообщение, отметка прочитано
										$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
											VALUES (NULL, '$user_id', 'С вашего счёта списана сумма в $salary р. для оплаты труда рабочих', '0', '0', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
										$mysqli->query("UPDATE `user_ip` SET `paid` = 1 WHERE `id` = '$ip_id'"); //paid = 1 - проверить
									}
									elseif ( ($salary > $row_user["coins"]) && ($row["paid"] == 0) )
									{
										$mysqli->query("UPDATE `users` SET `coins` = 0 WHERE `id` = '$user_id';");
										//Генерация события в журнал, в журнале отметка, создаёт ли это событие сообщение, отметка прочитано
										$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
											VALUES (NULL, '$user_id', 'Вам не хватило средств для выплаты з/п, рабочий ушёл от вас', '0', '0', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
										$mysqli->query("UPDATE `user_ip` SET `workers` = `workers` - 1, `paid` = 1 WHERE `id` = '$ip_id'"); //paid = 1 - проверить
										//Удаление любого бота из нанятых
										$res_worker = $mysqli->query("SELECT * FROM `ip_workers` WHERE `ip_id` = '$ip_id';");
										$row_worker = $res_worker->fetch_assoc(); //вернёт последний
										$worker_id = $row_worker["user_id"];
										$mysqli->query("DELETE FROM `ip_workers` WHERE `user_id` = '$worker_id' AND `ip_id` = '$ip_id';");
									}
									//КОНЕЦ АВТОЗП И ШТРАФА

									$local_date = timestampToLocal($mysqli);
									//ПОНИЖЕНИЕ МОТИВАЦИИ СОТРУДНИКОВ В ЗАВИСИМОСТИ ОТ УРОВНЯ (ЕСЛИ БОЛЬШЕ МЕСЯЦА НЕ УВЕЛИЧИВАЛ)
									if ($time["month"] - $row_user["update_lvl"] > 1 )
									{
										$size = 500;
										if ($time["month"] - $row_user["update_lvl"] > 2)
											$size = 1000;

										$mysqli->query("UPDATE `ip_workers` SET `motivation` = `motivation` - $size  WHERE `ip_id` = '$ip_id';");
										//Генерация события в журнал, в журнале отметка, создаёт ли это событие сообщение, отметка прочитано
										$monthText = ($size == 500) ? "месяца" : "двух месяцев";
										$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
											VALUES (NULL, '$user_id', 'Вы больше $monthText не повышали свой уровень, мотивация сотрудников понижена', '0', '0', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
									}
								}
								else
								{
									$mysqli->query("INSERT INTO `ip_tax` (`id`, `ip_id`, `tax_id`, `left`, `sum`) VALUES (NULL, '$ip_id', '1', '0', '$taxProfit');");
								}
							}
								// $rdate = $mysqli->query("SELECT * FROM `time`");
								// $time = $rdate->fetch_assoc();
						}
					}
					//КОНЕЦ ПОДСЧЕТА НАЛОГОВ И ШТРАФОВ ПО ЗП

					//обновление данных после начисления налогов подсчёта в конце месяца
					$mysqli->query("UPDATE `user_ip` SET `profit` = 0, `mine_profit` = 0, `paid` = 0,  `months` = `months` + 1;");
				}//КОНЕЦ НАСТУПЛЕНИЯ НОВОГО МЕСЯЦА

			}
			//КОНЕЦ НЕДЕЛИ, ДО МАГИЧЕСКОГО ВРЕМЕНИ РОВНО

			//ОБНОВЛЕНИЕ ПОКАЗАТЕЛЕЙ ПО РАБОЧИМ - общая прибыль

			//$room_cost = $gtime["room_cost"];
			//$mysqli->query("UPDATE `users` SET `coins` = `coins` - '$room_cost' WHERE `magic` = 1");
			$mysqli->query("UPDATE `time` SET `isMagic` = 1, `week_ended` = CURRENT_TIMESTAMP");
			// complete - 2, получил диплом, тогда по переменной prize понимаем, можнт ли забрать награду
			//$mysqli->query("UPDATE `user_achieve` SET `prize` = 1 WHERE `complete` = 2");
			//Магическое время до следущего запуска шедулером в начале реального дня
			//$mysqli->query("UPDATE `user_work` SET `turns` = 5");


			//ОПЛАТА ДОЛГОВ ПРИ НАЛИЧИИ И ВЫСЕЛЕНИЕ (КОНЕЦ НЕДЕЛИ)
			$res_apart = $mysqli->query("SELECT * FROM `user_apart` WHERE `debt` > 0");
			while ($row_apart = $res_apart->fetch_assoc())
			{
				$user_id = $row_apart["user_id"];
				$roomDebt = $row_apart["debt"];

				$res_user = $mysqli->query("SELECT * FROM `users` WHERE `id` = '$user_id';");
				$row_user = $res_user->fetch_assoc();
				$coins = $row_user["coins"];

				$local_date = timestampToLocal($mysqli);
				//Есть деньги оплатить задолжность
				if ($coins >= $roomDebt)
				{
					// Хватает денег
					$mysqli->query("UPDATE `users` SET `coins` = `coins` - $roomDebt  WHERE `id` = '$user_id'");
					$mysqli->query("UPDATE `user_apart` SET `debt` = '0' WHERE `user_id` = '$user_id'");
					//Вызывается с 1-й, значит будет показывать на сцене
					$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
						VALUES (NULL, '$user_id', 'Счёт за квартиру оплачен', '0', '1', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
				}
				else //недостаточно средств
				{
					//Удаление квартиры
					$mysqli->query("UPDATE `users` SET `magic` = '0' WHERE `id` = '$user_id'");
					$mysqli->query("DELETE FROM `user_apart` WHERE `user_id` = '$user_id'");
					//Запись об этом в журнал
					$mysqli->query("INSERT INTO `journal` (`id`, `user_id`, `text`, `read`, `show`, `icon`, `scene`, `created`, `updated`, `strdate`)
						VALUES (NULL, '$user_id', 'К сожалению, мы вынужденный выселить вас за неуплату', '0', '1', NULL, 'game', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$local_date');");
				}
			}

			//КОНЕЦ NEW_DAY
		}//КОНЕЦ ЕСТЬ ПОДКЛЮЧЕНИЕ К DB
	}
	else
	{//НЕВЕРНО УКАЗАН KEY, ЗНАЧИТ ИЗВНЕ ПЫТАЛИСЬ ЗАПУСТИТЬ
		$out["msg"] = "Authorization error, wrong hash";
		exit;
	}
?>
