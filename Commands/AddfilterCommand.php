<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

require_once __DIR__.'/../FilterDB.php';
use FilterDB;

/**
 * Addfilter command
 */
class AddfilterCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'addfilter';

    /**
     * @var string
     */
    protected $description = 'Добавляет слово, по которому фильтруются проекты fl.ru. ';

    /**
     * @var string
     */
    protected $usage = '/addfilter <word>';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = true;

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
        $word = trim($message->getText(true));
        
        if($word === '') {
            $text = 'Использование комманды: ' . $this->getUsage();
        } else {
            $text = "Добавленн фильтр по слову \"$word\"";
            FilterDB::initializeFilter();
            FilterDB::insertFilter($chat_id, $word);
        }
        
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
