<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

require_once __DIR__.'/../FilterDB.php';
use FilterDB;

/**
 * Clearfilters command
 */
class ClearfiltersCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'clearfilters';

    /**
     * @var string
     */
    protected $description = 'Очищает фильтры, по которомым фильтруются проекты fl.ru. ';

    /**
     * @var string
     */
    protected $usage = '/clearfilters';

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
        $text = "Фильтры проектов очищены.";
        
        FilterDB::initializeFilter();
        FilterDB::deleteFilter($chat_id);
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
