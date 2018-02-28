<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\InlineKeyboard;

require_once __DIR__.'/../FilterDB.php';
use FilterDB;

/**
 * Filters command
 */
class FiltersCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'filters';

    /**
     * @var string
     */
    protected $description = 'Очищает фильтры, по которомым фильтруются проекты fl.ru. ';

    /**
     * @var string
     */
    protected $usage = '/filters';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;
    
    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
    
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

        $data = [
            'chat_id'      => $chat_id,
            'text'         => 'Управление фильтрами.',
            'reply_markup' => $inline_keyboard
        ];

        return Request::sendMessage($data);
    }
}
