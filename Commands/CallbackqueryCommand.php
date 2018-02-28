<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;

require_once __DIR__.'/../FilterDB.php';
use FilterDB;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query = $this->getCallbackQuery();
        $callback_query_id = 0;
        $callback_data = '';

        if($callback_query) {
            $message = $callback_query->getMessage();    
            $callback_query_id = $callback_query->getId();
            $callback_data     = $callback_query->getData();
            $user = $callback_query->getFrom();
        } else {
            $message = $this->getMessage();
            $user = $message->getFrom();
        }
        
        $chat = $message->getChat();
        $text = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        print $this->getName().PHP_EOL;
        print $callback_data.PHP_EOL;
        print "user_id $user_id\n";
        print "chat_id $chat_id\n";

        @list($command, $command_data) = explode(" ", $callback_data, 2);
        $result = Request::emptyResponse();
        FilterDB::initializeFilter();

        switch ($command) {

            case 'filter_remove_all':
        
                FilterDB::deleteFilter($chat_id);
                
                // delete previous keyboard
                $result = Request::deleteMessage([
                    'chat_id'    => $chat_id,
                    'message_id' => $message->getMessageId(),
                ]);

                // send new keyboard
                $result = $this->showFiltersKeyboard();

                $result = Request::answerCallbackQuery([
                    'callback_query_id' => $callback_query_id,
                    'text'              => 'Все фильтры удалены.',
                    'show_alert'        => true,
                    'cache_time'        => 5,
                ]);

                break;
                
            case 'filter_add':

                // make conversation and redirect next word to Commands/AddfilterCommand.php
                $conversation = new Conversation($user_id, $chat_id, 'addfilter');
                $conversation->update();

                // delete previous keyboard
                $result = Request::deleteMessage([
                    'chat_id'    => $chat_id,
                    'message_id' => $message->getMessageId(),
                ]);

                $result = Request::sendMessage([
                    'chat_id'      => $chat_id,
                    'text'         => 'Введите слово или фразу: '
                ]);

                break;

            case 'filter_remove':

                $id = intval($command_data);

                FilterDB::deleteFilter($chat_id, $id);

                // delete previous keyboard
                Request::deleteMessage([
                    'chat_id'    => $chat_id,
                    'message_id' => $message->getMessageId(),
                ]);

                // send new keyboard
                $result = $this->showFiltersKeyboard();

                $result = Request::answerCallbackQuery([
                    'callback_query_id' => $callback_query_id,
                    'text'              => 'Фильтр удален.',
                    'show_alert'        => true,
                    'cache_time'        => 5,
                ]);

                break;
            
            default:
                # code...
                break;
        }

        return $result;
    }

    /**
     * Show filters keyboard
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    private function showFiltersKeyboard()
    {
        $callback_query    = $this->getCallbackQuery();
        $chat_id = $callback_query->getMessage()->getChat()->getId();

        FilterDB::initializeFilter();
        $filters = FilterDB::selectFilter($chat_id);

        $keyboard_buttons = [];

        foreach ($filters as $filter) {
            $keyboard_buttons[] = [
                ['text' => "\xE2\x9E\x96 ".$filter['word'], 'callback_data' => 'filter_remove '.$filter['id']]
            ];
        }

        $keyboard_buttons[] = [
            ['text' => "\xE2\x9E\x95 Добавить", 'callback_data' => 'filter_add'],
            ['text' => "\xE2\x9D\x8C Удалить все", 'callback_data' => 'filter_remove_all'],
        ];

        $class_name = '\Longman\TelegramBot\Entities\InlineKeyboard';
        $inline_keyboard = new $class_name(...$keyboard_buttons);

        return Request::sendMessage([
            'chat_id'      => $chat_id,
            'text'         => 'Управление фильтрами.',
            'reply_markup' => $inline_keyboard
        ]);
    }
}
