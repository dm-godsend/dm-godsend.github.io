<?php

function get_curl($url)
{
   if(function_exists('curl_init')) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $output = curl_exec($ch);
      //echo $output;
      //echo curl_error($ch);
      curl_close($ch);
      return $output;
   }
   else
   {
      return file_get_contents($url);
   }
}

$sr = "213.159.211.104"; //или "localhost" - нужно тестировать в реале
$un = "user";

$key=$argv[1];
$pw = "T3n6L2q9K3f6D5r9C8p6T4e7";
$db = "db_cash";

if (md5($key) === md5("T3n6L2q9K3f6D5r9C8p6T4e7"))
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
      $user_ids = array();
      $res = array();

      for ($i = 1; $i<= 4; $i++)
      {
         $user_ids[$i] = "";
         $res[$i] = $mysqli->query("SELECT `vk_id` FROM `users` WHERE `vk_id` > 100 AND `status_id` = '$i' ORDER BY `vk_id` ASC");

         $text = "";
         if ($i == 1) {
            $text = "РАБОТА НА ШАХТЕ ЖДЁТ!";
         } elseif ($i == 2) {
            $text = "Хотите заработать? Вперёд в Игру!";
         } elseif ($i == 3) {
            $text = "Вас ждут на Бирже!";
         } elseif ($i == 4) {
            $text = "У Вас ещё больше денег!";
         }
         $msg = str_replace(' ','%20', $text);

         $cnt = 0;
         while ($row = $res[$i]->fetch_assoc())
         {
            $cnt++;
            $user_ids[$i].= $row["vk_id"].",";
            if ($cnt == 99)
            {
               //aab8ff89aab8ff89aab8ff8962aad6ce55aaab8aab8ff89f7634ea3ecc9c28f8ddcfbf8
               //5ced87855ced87855ced8785335c82f5fa55ced5ced878502d801aff0ff5b786fb5c8d4
               $user_ids[$i] = substr( $user_ids[$i], 0, strlen($user_ids[$i]) - 1 );
               $str = "https://api.vk.com/method/secure.sendNotification?user_ids=".$user_ids[$i]."&message=".$msg."&v=5.37&access_token=eb68a034eb68a034eb68a034a8eb0691e8eeb68eb68a034b5957e1d0b519d1bd293db13";
               get_curl($str);
               //echo $user_ids[$i]."</br>";
               $user_ids[$i] = "";
               $cnt = 0;
            }
         }
         if ($user_ids[$i] != "")
         {
            $user_ids[$i] = substr( $user_ids[$i], 0, strlen($user_ids[$i]) - 1 );
            $str = "https://api.vk.com/method/secure.sendNotification?user_ids=".$user_ids[$i]."&message=".$msg."&v=5.37&access_token=eb68a034eb68a034eb68a034a8eb0691e8eeb68eb68a034b5957e1d0b519d1bd293db13";
            get_curl($str);
            //echo $user_ids[$i]."</br></br>";
         }
      }

   }
}

?>
