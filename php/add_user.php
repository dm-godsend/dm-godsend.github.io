<?php

include 'db_connect.php';

$out["flag"] = 0;
$out["usr"] = array();
//$out["time"] = date("H:i");

// $id = $_POST["id"];
$name = $_POST["name"];

$ava = $_POST["ava"];
$gender = $_POST["gender"];
$appear = $_POST["appear"];
$bday = $_POST["bday"];
$vk_id = $_POST["vk_id"];

$qwery_find = "SELECT * FROM `users` WHERE `name` = '$name'";
$res_find = $mysqli->query($qwery_find);

if ($row_find = $res_find->fetch_assoc())
{ //такое имя уже есть
	$out["flag"] = 0;
	//Понять как точнее определять успешный запрос, результат из базы
	$out["msg"] = "Name is already in use";
}
else
{
	//не встречается, добавить новому пользователю
	//Health - поправить
	$qwery_add = "INSERT INTO `users` (`id`, `ava`, `name`, `gender`, `appear`, `status`, `magic`, `exp`, `health`, `joy`, `coins`, `lvl`, `points`, `help`, `bday`, `vk_id` )
		VALUES (NULL, '$ava', '$name', '$gender', '$appear', 'Физическое лицо', '0', '0', '1', '1', '1500', '1', '0', '5', '$bday', '$vk_id');";
	$res_add = $mysqli->query($qwery_add);

	// Поиск добавленного по имени
	$qwery_find = "SELECT * FROM `users` WHERE `name` = '$name'";
	$res_find = $mysqli->query($qwery_find);

	// В row - результат добавления нового пользователя
	if ($row = $res_find->fetch_assoc()) //первый элемент с наибольшим id
	//после полного цикла в $row - последний (т.е добавленный только что)
	{
		//Добавление записи в таблицу параметров
		$user_id = $row["id"];

		$qwery_params = "INSERT INTO `user_params` (`id`, `user_id`, `lang`, `sound`, `music`, `note`, `game`, `employ`, `treasure`, `mine`, `room`, `bank`, `tax`, `sambl`, `portal`, `grandma`, `school`, `story_id`, `current_id`, `mom`, `created`, `updated`)
			VALUES (NULL, '$user_id', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '-1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);";
		$res_params = $mysqli->query($qwery_params);

		//Добавление стандартных предметов в квартиру
		$qwery1 = "INSERT INTO `user_stock` (`id`, `item_id`, `space`, `user_id`, `is_active`, `is_new`, `left_days`) VALUES (NULL, '1', '1', '$user_id', '1', '1', '0');";
	   $qwery2 = "INSERT INTO `user_stock` (`id`, `item_id`, `space`, `user_id`, `is_active`, `is_new`, `left_days`) VALUES (NULL, '3', '1', '$user_id', '1', '1', '0');";
	   $qwery3 = "INSERT INTO `user_stock` (`id`, `item_id`, `space`, `user_id`, `is_active`, `is_new`, `left_days`) VALUES (NULL, '8', '1', '$user_id', '1', '1', '0');";
		$mysqli->query($qwery1);
		$mysqli->query($qwery2);
		$mysqli->query($qwery3);

		//ДОБАВЛЕНИЕ ЗАПИСЕЙ В ТАБЛИЦУ НАВЫКОВ ПОЛЬЗОВАТЕЛЯ
		$res_abil = $mysqli->query("SELECT * FROM `abilities`");
		$abilities = array();
		while ($row_abil = $res_abil->fetch_assoc())
			array_push($abilities, $row_abil["id"]);

		$qwery_abil = "";
		foreach ($abilities as $ability_id)
			$qwery_abil .= "INSERT INTO `user_ability` (`id`, `ability_id`, `user_id`, `value`) VALUES (NULL, '$ability_id', '$user_id', '0');";

		if ($mysqli->multi_query($qwery_abil) == TRUE)
		{
			$out["flag"] = 1;
			$out["msg"] = "Successfully adding user ability info ".$user_id;
			//Проверить, может стоит загрузить параметры пользователя, чтобы работчало всё до обновления
		}
		//КОНЕЦ ДОБАВЛЕНИЯ НАВЫКОВ

		// //ЗАГРУЗКА НАВЫКОВ ПОЛЬЗОВАТЕЛЯ
		// $row["ability"] = array();
		// $res_ability = $mysqli->query("SELECT * FROM `user_ability` WHERE `user_id`='$user_id'");
		// while ($row_ability = $res_ability->fetch_assoc())
		// 	array_push($row["ability"], $row_ability);
		//
		// //ЗАГРУЗКА ПАРАМЕТРОВ ПОЛЬЗОВАТЕЛЯ
		// $res_param = $mysqli->query("SELECT * FROM `user_params` WHERE `user_id`='$user_id'");
		// if ($row_param = $res_param->fetch_assoc())
		// {
		// 	$row["params"] = $row_param;
		// }
		//
		// //стандартные параметры нового пользователя (те, что обычно генерируются при загрузке
		// $row["skills"] = array(0,0,0,0);
		// //Это означает, что пользователь новый и это его первый вход, будет только при первом входе (при обновлении - исчезнет)
		//

		// $out["msg"] = "Successful adding user";
		// $out["first"] = 1;
		//
		// $out["usr"] = $row;
	}
	else // Не удалось добавить нового пользователя (в базе его нет после запроса)
	{
		$out["flag"] = 0;
		$out["msg"] = "Error adding new user, try to repeat";
	}
}

echo json_encode($out);

?>
