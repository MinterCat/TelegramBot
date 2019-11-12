<?php
const TOKEN = '';
$key = 'MinterCat';
const BASE_URL = 'https://api.telegram.org/bot'.TOKEN.'/';
$method = 'setWebhook';
$url = BASE_URL . $method;
$options = [
	'url' => "https://YOURSITE/bot/$key.php",
];

$response = file_get_contents($url . '?' . http_build_query($options));

var_dump($response);
?>





