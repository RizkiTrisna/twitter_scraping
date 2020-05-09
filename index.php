<?php
set_time_limit(0);
session_start();
require_once("oauth/twitteroauth/twitteroauth.php"); //Path to twitteroauth library
require_once 'db_config.php';
require_once 'inputJsonToDatabase.php';

$twitteruser = "rizkitrisna_ra";
$notweets = 30;
$consumerkey = "e797FjURPZkMH3AcduhIhZ5Ob";
$consumersecret = "AlSCAPPIWm4qev8wbRBQVuE4ZcadVjaD1HWMjx22Jlfnnz85EV";
$accesstoken = "918947738-pmg8XWxMZkUXLbVKAlXsRfCdZGuXiHbL8ViKxU9m";
$accesstokensecret = "IByzM4qhewqLvfUWRP2HpAENxiLQTlHrfF2apXS7rZ54I";

function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret)
{
    $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
    return $connection;
}

function getData($words, $id_kota)
{
    global $consumerkey;
    global $consumersecret;
    global $accesstoken;
    global $accesstokensecret;
    global $pdo_con;

    echo "getting access...";
    echo "<br>";
    $connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
    $next_url = "";
    $success_count = 0;
    $failed_count = 0;
    $year = "";
    $month = "";
    $date = "";

    //Mencari lokasi lat dan lon berdasar id kota
    $id_kota = $id_kota;
    $pdo_statement_loc = $pdo_con->prepare("SELECT * FROM kota WHERE id_kota=:id_kota");
    $pdo_statement_loc->bindParam(":id_kota", $id_kota);
    $pdo_statement_loc->execute();
    $result_loc = $pdo_statement_loc->fetchAll();
    $lat = $result_loc[0]['lat'];
    $lon = $result_loc[0]['lon'];
    $range = $result_loc[0]['radius_cari'];
    //Inisialisasi awal tanggal
    $end_date = date("Y-M-d", strtotime("today -7 days"));
    $current_date = date("Y-M-d", strtotime("today"));
    $message = "";
    //Pencarian tweet
    $tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=$words&geocode=$lat,$lon," . $range . "km&count=95");
    $hasil_encode = json_encode($tweets);
    $hasil_decode = json_decode(json_encode($tweets), true);
    echo "access established!";
    echo "<br>";
    if (isset($hasil_decode["statuses"]) && isset($hasil_decode["search_metadata"]["next_results"])) {
        echo "start crawling...";
        echo "<br>";
        if (inputCrawlToDatabase($hasil_encode, $id_kota)) {
            $next_url = $hasil_decode["search_metadata"]["next_results"];
            $success_count += 1;

            $year = substr($hasil_decode["statuses"][0]['created_at'], -5);
            $month = substr($hasil_decode["statuses"][0]['created_at'], 4, 3);
            $date = substr($hasil_decode["statuses"][0]['created_at'], 8, 2);

            $current_date = date("Y-M-d", strtotime($month . " " . $date . " " . $year));
            $status = true;
            $isset_statuses = 1;
            while (($end_date != $current_date) && $status && $isset_statuses) {

                $tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json" . $next_url);
                $hasil_encode = json_encode($tweets);
                $hasil_decode = json_decode(json_encode($tweets), true);

                // Salah di next url nya, coba cek++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                if (($isset_statuses = isset($hasil_decode["statuses"])) && isset($hasil_decode["search_metadata"]["next_results"]) && ($status = inputCrawlToDatabase($hasil_encode, $id_kota))) {

                    try {
                        $next_url = $hasil_decode["search_metadata"]["next_results"];
                        $success_count += 1;
                        $year = substr($hasil_decode["statuses"][0]['created_at'], -5);
                        $month = substr($hasil_decode["statuses"][0]['created_at'], 4, 3);
                        $date = substr($hasil_decode["statuses"][0]['created_at'], 8, 2);

                        $current_date = date("Y-M-d", strtotime($month . " " . $date . " " . $year));
                    } catch (Exception $e) {
                        $e->getMessage();
                    }
                } else {
                    $message = "hasil_decode['statuses'] di if dalam while belum di set";
                }
            }
        }
    } else if (isset($hasil_decode['errors'][0]['message'])) {
        $message = $hasil_decode['errors'][0]['message'];
    } else {
        $message = "hasil_decode['statuses'] di if luar while belum di set";
    }
    $message .= " - Last URL : " .  $next_url . " On id_kota : " . $id_kota;

    return $message;
}

function start()
{
    global $pdo_con;

    $words = 'Pendidikan';
    echo "Note : " . getData($words, 11);
    echo "<br>";
}

start();
