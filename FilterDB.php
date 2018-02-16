<?php

// namespace FakeNPC\FlRuTelegramBot;
// namespace Longman\TelegramBot;

//use Exception;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\DB;
//use PDO;

class FilterDB extends DB
{
    /**
     * Initialize filter table
     */
    public static function initializeFilter()
    {
        if (!defined('TB_FILTER')) {
            define('TB_FILTER', self::$table_prefix . 'filter');
        }
    }

    /**
     * Select a data from the DB
     *
     * @param string   $user_id
     * @param string   $chat_id
     * @param int|null $limit
     *
     * @return array|bool
     * @throws TelegramException
     */
    public static function selectFilter($chat_id = null, $limit = null)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sql = '
              SELECT *
              FROM `' . TB_FILTER . '`
            ';

            $where = array();

            if($chat_id !== null) {
                $where[] = '`chat_id` = :chat_id';
            }

            if(count($where)) {
                $sql .= ' WHERE '.join(' AND ', $where);
            }

            if ($limit !== null) {
                $sql .= ' LIMIT :limit';
            }

            $sth = self::$pdo->prepare($sql);

            if($chat_id !== null) {
                $sth->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
            }

            if ($limit !== null) {
                $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
            }

            $sth->execute();

            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new TelegramException($e->getMessage());
        }
    }

    /**
     * Insert the data in the database
     *
     * @param string $chat_id
     * @param string $run_timestamp
     * @param string $runned
     *
     * @return bool
     * @throws TelegramException
     */
    public static function insertFilter($chat_id, $word)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sth = self::$pdo->prepare('INSERT INTO `' . TB_FILTER . '`
                (`chat_id`, `word`)
                VALUES
                (:chat_id, :word)
            ');

            // $date = self::getTimestamp();

            $sth->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
            $sth->bindValue(':word', $word, PDO::PARAM_STR);

            return $sth->execute();
        } catch (Exception $e) {
            throw new TelegramException($e->getMessage());
        }
    }

    public static function deleteFilter($chat_id)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sth = self::$pdo->prepare('DELETE FROM `' . TB_FILTER . '`
                WHERE `chat_id` = :chat_id
            ');

            // $date = self::getTimestamp();

            $sth->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);

            return $sth->execute();
        } catch (Exception $e) {
            throw new TelegramException($e->getMessage());
        }
    } 
}
