<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

require_once __DIR__.'/../RunDB.php';
use RunDB;

/**
 * Stop command
 */
class StopCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'stop';

    /**
     * @var string
     */
    protected $description = 'Выключает парсинг и отправку вам fl.ru проектов. ';

    /**
     * @var string
     */
    protected $usage = '/stop';

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
        $text = 'Отправка проектов остановленна. ';

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        RunDB::initializeRun();
        RunDB::replaceRun($chat_id, time(), 0);

        return Request::sendMessage($data);
    }
}
