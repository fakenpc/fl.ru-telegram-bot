<?php

set_time_limit(0);
ini_set('display_errors','on');
ignore_user_abort(true);
// Use internal libxml errors -- turn on in production, off for debugging
libxml_use_internal_errors(true);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/ProjectDB.php';
require_once __DIR__.'/RunDB.php';
require_once __DIR__.'/FilterDB.php';
use Longman\TelegramBot\Request;
ProjectDB::initializeProject();
RunDB::initializeRun();
FilterDB::initializeFilter();

if(!file_exists('config.php')) {
    die("Please rename example_config.php to config.php and try again. \n");
} else {
    require_once 'config.php';
}

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
    // Add commands paths containing your custom commands
    $telegram->addCommandsPaths($commands_paths);
    $telegram->enableLimiter();
    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

	$url = 'https://www.fl.ru/projects/';
	$response = curlRequest($url);
	//print $response;

	// parse fl.ru projects
	$dom = new DomDocument;
	$dom->loadHTML($response);
	$xpath = new DomXPath($dom);
	$domNodeListItems = $xpath->query("//a[@class='b-post__link']");

	for($i = 0; $i < $domNodeListItems->length; $i++) {
		$href = $domNodeListItems->item($i)->attributes->getNamedItem("href")->nodeValue;
		$name = $domNodeListItems->item($i)->attributes->getNamedItem("name")->nodeValue;
		$id = substr($name, 3);
		$url = 'https://www.fl.ru'.$href;
		$name = '';
		$body = '';
		$projects = ProjectDB::selectProject($id);
		// if project not parsed
		if($projects !== false && count($projects) == 0) {

			$response = curlRequest($url);

			// parse project name
			$dom = new DomDocument;
			$dom->loadHTML($response);
			$xpath = new DomXPath($dom);
			$tempDomNodeListItems = $xpath->query("//h1[@id='prj_name_{$id}']");

			if($tempDomNodeListItems->length) {
				$name = $tempDomNodeListItems->item(0)->nodeValue;
				$name = trim($name);
			}

			// parse project body
			$dom = new DomDocument;
			$dom->loadHTML($response);
			$xpath = new DomXPath($dom);
			$tempDomNodeListItems = $xpath->query("//div[@id='projectp{$id}']");

			if($tempDomNodeListItems->length) {
				$body = $tempDomNodeListItems->item(0)->nodeValue;
				$body = trim($body);
			}

			$runned = RunDB::selectRun(null, 1);
			
			foreach ($runned as $run) {
				$chat_id = $run['chat_id'];

				$filters = FilterDB::selectFilter($chat_id);
				// $words = array();

				foreach ($filters as $filter) {
					$word = $filter['word'];
					// $words[] = $filter['word'];

					if(preg_match('/'.$word.'/iu', $name) || preg_match('/'.$word.'/iu', $body)) {
						
						$text = "$name \n\n ".mb_strimwidth($body, 0, 320)."... \n $url";

						$data = [
						    'chat_id' => $chat_id,
						    'text'    => $text,
						];

						Request::sendMessage($data);
						break;
					}
				
				}

				if(count($filters) == 0) {
					$text = "$name \n\n ".mb_strimwidth($body, 0, 320)."... \n $url";

					$data = [
					    'chat_id' => $chat_id,
					    'text'    => $text,
					];

					Request::sendMessage($data);
				}
			}
			// cache new project id's
			ProjectDB::insertProject($id, 1);
			//ProjectDB::updateProject(array('sended' => 1), array('id' => $chat_id));
		}
	}

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    echo $e->getMessage();
}

function curlRequest($url, $postFields = false, $customHeaders = array(), $cookiesFilename = 'cookies')
{
	// create curl resource 
	$ch = curl_init(); 

	// set url 
	curl_setopt($ch, CURLOPT_URL, $url); 

	//return the transfer as a string 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiesFilename);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiesFilename);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.119 Chrome/64.0.3282.119 Safari/537.36');
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);

	if ($postFields !== false)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	}

	// $output contains the output string 
	$output = curl_exec($ch); 
	//print_r(curl_getinfo($ch));

	// close curl resource to free up system resources 
	curl_close($ch);      

	return $output;
}
?>
