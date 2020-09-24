<?php
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: *');
	header('Access-Control-Allow-Credentials: true');

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

	$sr = "sql7.freemysqlhosting.net";
	$un = "sql7367136";
	$pw = "DyIkunK7Kt";
	$db = "sql7367136";

	if (true)
	{
		$out["flag"] = 0;
		$out["msg"] = "Server unreachable";

		$mysqli = new mysqli($sr, $un, $pw, $db);
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
			$start_day = ($tm - $nowdt);

			$out["time"] = $start_day;
		}
	}
	else
	{
		$out["msg"] = "Authorization error, wrong hash";
		echo $out["msg"];
		exit;
	}
?>
