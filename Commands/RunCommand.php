<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

require_once __DIR__.'/../RunDB.php';
use RunDB;

/**
 * Run command
 */
class RunCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'run';

    /**
     * @var string
     */
    protected $description = 'Включает парсинг и отправку вам fl.ru проектов. ';

    /**
     * @var string
     */
    protected $usage = '/run';

    /**
     * @var string
     */
    protected $version = '1.1.0';

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
        $text = "Отправка проектов запущенна. \n Используйте комманду /addfilter <word>, чтобы отфильтровать интересующие проекты.";
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        RunDB::initializeRun();
        RunDB::replaceRun($chat_id, time(), 1);

        return Request::sendMessage($data);
    }
}
