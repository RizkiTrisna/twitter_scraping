<?php
session_start();
require_once("oauth/twitteroauth/twitteroauth.php"); //Path to twitteroauth library

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

$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);

$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?max_id=1252920679738949632&q=Pendidikan&geocode=-7.966652%2C112.632623%2C12km&count=100&include_entities=1&result_type=recent");

echo json_encode($tweets);
