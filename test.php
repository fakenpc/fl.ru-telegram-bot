<?php

set_time_limit(0);
ini_set('display_errors','on');
ignore_user_abort(true);
// Use internal libxml errors -- turn on in production, off for debugging
libxml_use_internal_errors(true);

require_once __DIR__ . '/vendor/autoload.php';
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;

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

    $chat_id = 327350583;

    $switch_element = mt_rand(0, 9) < 5 ? 'true' : 'false';


    // пример inline keyboards кнопок https://core.telegram.org/bots#inline-keyboards-and-on-the-fly-updating
    $inline_keyboard = new InlineKeyboard([
        ['text' => 'inline', 'switch_inline_query' => $switch_element],
        ['text' => 'inline current chat', 'switch_inline_query_current_chat' => $switch_element],
    ], [
        ['text' => 'callback', 'callback_data' => 'identifier'],
        ['text' => 'open url', 'url' => 'https://github.com/php-telegram-bot/core'],
    ]);

    $data = [
        'chat_id'      => $chat_id,
        'text'         => 'inline keyboard',
        'reply_markup' => $inline_keyboard,
    ];

    //Request::sendMessage($data);

    // пример replykeyboardmarkup https://core.telegram.org/bots#keyboards
    $keyboard = new Keyboard(
	    [
	        'keyboard'          => [['Yes', 'No']],
	        'resize_keyboard'   => true,
	        'one_time_keyboard' => true,
	        'selective'         => false,
	    ]
	);

    $data = [
        'chat_id'      => $chat_id,
        'text'         => 'inline keyboard',
        'reply_markup' => $keyboard,
    ];

    Request::sendMessage($data);

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    echo $e->getMessage();
}

?>
