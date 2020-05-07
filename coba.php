<?php
require_once 'db_config.php';


// $data = file_get_contents('ehe2.json');
// $data_decode = json_decode($data, true);

// $year = substr($data_decode["statuses"][0]['created_at'], -5);
// $month = substr($data_decode["statuses"][0]['created_at'], 4, 3);
// $date = substr($data_decode["statuses"][0]['created_at'], 8, 2);

// echo date("Y-M-d", strtotime("today -7 days"));
// echo date("Y-M-d", strtotime("today"));
// // $end_date = date("Y-M-d", strtotime($month . " " . $date . " " . $year . " -7 days"));
// $end_date = date("Y-M-d", strtotime("today -7 days"));
// $current_date = date("Y-M-d", strtotime("today"));

// $a = 0;
// while ($end_date != $current_date) {
//     echo ++$a;
//     $current_date = date("Y-M-d", strtotime("today -$a days"));
// }
// echo $current_date;
