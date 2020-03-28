<?php
header('Content-Type: text/html; charset=utf-8');
// подрубаем API
require_once("vendor/autoload.php");

// дебаг
if(true){
	error_reporting(E_ALL & ~(E_NOTICE | E_USER_NOTICE | E_DEPRECATED));
	ini_set('display_errors', 1);
}

// создаем переменную бота
$token = "";
$bot = new \TelegramBot\Api\Client($token,null);

// если бот еще не зарегистрирован - регистируем
if(!file_exists("registered.trigger")){ 
	/**
	 * файл registered.trigger будет создаваться после регистрации бота. 
	 * если этого файла нет значит бот не зарегистрирован 
	 */
	 
	// URl текущей страницы
	$page_url = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$result = $bot->setWebhook($page_url);
	if($result){
		file_put_contents("registered.trigger",time()); // создаем файл дабы прекратить повторные регистрации
	} else die("ошибка регистрации");
}

// обязательное. Запуск бота
$bot->command('start', function ($message) use ($bot) {

$text = 'Здравствуйте!

Для того, чтоб проверить своих МинтерКотов, просто отправьте адрес своего кошелька боту.

Для получения полной инструкции отправьте 
/help
';

	$bot->sendMessage($message->getChat()->getId(),  $text);
	$bot->answerCallbackQuery($callback->getId()); // можно отослать пустое, чтобы просто убрать "часики" на кнопке
});
//=============================
// Команды бота
$bot->command('myid', function ($message) use ($bot) {
	$getid = $message->getChat()->getId();	
	$bot->sendMessage($getid, $getid);
	$bot->answerCallbackQuery($callback->getId());
});
//=============================
// помощ
$bot->command('help', function ($message) use ($bot) {
    $answer = '
🐈 Мои котики
Для того, чтоб увидеть список своих МинтерКотов, отправьте боту свой адрес кошелька

🐈 Описание котика
Чтобы получить полное описание МинтерКота, отправьте боту его имя.
    ';
    $bot->sendMessage($message->getChat()->getId(), $answer);
    $bot->answerCallbackQuery($callback->getId());
});


$bot->on(function($Update) use ($bot){
	
	$message = $Update->getMessage();
	$mtext = $message->getText();
	$cid = $message->getChat()->getId();
//=============================	

if(mb_stripos($mtext,"Mx") !== false)
  {
    $getid = $message->getChat()->getId();
    //===============================    
    
$json = file_get_contents("https://mintercat.com/api/cats?addr=$mtext");
$payloads = json_decode($json,true);
$result = count($payloads);

for ($i = 0; $i <= $result; $i+=1)
{
$id = $payloads[$i]['stored_id']; 
		if ($id != "")
	{	$ii = $i + 1;

if($ii % 30 == 0) {
$t = "&";
}else{
$t = "";
}

$json4 = file_get_contents("https://mintercat.com/api");
$payloads4 = json_decode($json4,true);
$count4 = $payloads4['count'];

$json2 = file_get_contents("https://mintercat.com/api/cats?id=$id");
$payloads2 = json_decode($json2,true);
$img = $payloads2[0]['img'];



$cats = $payloads4['cats'];
$ccount = count($cats);
for ($y = 0; $y<=$ccount;$y+=1)
{
	$catimg = $payloads4['cats'][$y]['img'];
	if ($catimg == $img)
	{
		$rarity = $payloads4['cats'][$y]['rarity'];
		$rarity = $rarity * 100;
		$price = $payloads4['cats'][$y]['price'];
		$name = $payloads4['cats'][$y]['name'];
	}
}

$text = "
#$ii Котик /id$id
Порода <$name>
Примерная стоимость - $price MINTERCAT
$t";
	}}
	$count = count(explode("&", $text));
for ($i = 0; $i <= $count; $i++) {
	$textMessage = $text[$i];
	$bot->sendMessage($getid, $textMessage);
	}
//else{$text = "На данном кошельке нет МинтерКотов.Отправьте боту команду /help , чтобы узнать, как получить своего первого МинтерКотика.";$bot->sendMessage($getid, $text);}
//===============================   
}

if(mb_stripos($mtext,"/id") !== false)
  {
$json4 = file_get_contents("https://mintercat.com/api");
$payloads4 = json_decode($json4,true);
$count4 = $payloads4['count'];
    $id = explode("/id", $mtext)[1];
    $getid = $message->getChat()->getId();
    //--------------------------
$json = file_get_contents("https://mintercat.com/api/cats?id=$id");

$payloads = json_decode($json,true);
$addr = $payloads[0]['addr'];

 if ($addr != "") {

$img = $payloads[0]['img'];
$json2 = file_get_contents("http://api.minter.one/block?height=$id");
$payloads2 = json_decode($json2,true);

$cats = $payloads4['cats'];
$ccount = count($cats);
for ($y = 0; $y<=$ccount;$y+=1)
{
	$catimg = $payloads4['cats'][$y]['img'];
	if ($catimg == $img)
	{
		$rarity = $payloads4['cats'][$y]['rarity'];
		$rarity = $rarity * 100;
		$price = $payloads4['cats'][$y]['price'];
		$name = $payloads4['cats'][$y]['name'];
	}
}

$json3 = file_get_contents("https://mintercat.com/api/coin");
$payloads3 = json_decode($json3,true);
$bip = $payloads3['estimate']; 

$bip = $bip * $price;

$bip = round($bip,2);

$data = $payloads2['result']['time'];
$nd = date('d.m.Y', strtotime(explode('T', $data)[0]));
$text = "
Котик по имени /id$id
Порода <$name>
Хозяин котика $addr
Котик создан в блоке $id
$nd
Шанс выпадения $rarity%

Примерная стоимость МинтерКота - $price MINTERCAT

~ $bip BIP
";

 $pic = "https://mintercat.com/img/Cat$img.webp"; //webp or png

    $bot->sendPhoto($getid, $pic, $text);
    $bot->answerCallbackQuery($callback->getId());
}
//else{$text = "Котик с таким именем не создан!";$bot->sendMessage($getid, $text);$bot->answerCallbackQuery($callback->getId());}
}
//=============================	
}, function($message) use ($name){
	return true; // когда тут true - команда проходит
});

// запускаем обработку
$bot->run();

echo "бот";
