<?php

// namespace FakeNPC\FlRuTelegramBot;
// namespace Longman\TelegramBot;

//use Exception;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\DB;
//use PDO;

class RunDB extends DB
{
    /**
     * Initialize run table
     */
    public static function initializeRun()
    {
        if (!defined('TB_RUN')) {
            define('TB_RUN', self::$table_prefix . 'run');
        }
    }

    /**
     * Select a runned from the DB
     *
     * @param string   $user_id
     * @param string   $chat_id
     * @param int|null $limit
     *
     * @return array|bool
     * @throws TelegramException
     */
    public static function selectRun($chat_id = null, $runned = null, $limit = null)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sql = '
              SELECT *
              FROM `' . TB_RUN . '`
            ';

            $where = array();

            if($chat_id !== null) {
                $where[] = '`chat_id` = :chat_id';
            }

            if($runned !== null) {
                $where[] = '`runned` = :runned';
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

            if($runned !== null) {
                $sth->bindValue(':runned', $runned, PDO::PARAM_INT);

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
     * Insert the run in the database
     *
     * @param string $chat_id
     * @param string $run_timestamp
     * @param string $runned
     *
     * @return bool
     * @throws TelegramException
     */
    public static function insertRun($chat_id, $run_timestamp, $runned)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sth = self::$pdo->prepare('INSERT INTO `' . TB_RUN . '`
                (`chat_id`, `run_timestamp`, `runned`)
                VALUES
                (:chat_id, :run_timestamp, :runned)
            ');

            // $date = self::getTimestamp();

            $sth->bindValue(':chat_id', $chat_id);
            $sth->bindValue(':run_timestamp', $run_timestamp);
            $sth->bindValue(':runned', $runned);

            return $sth->execute();
        } catch (Exception $e) {
            throw new TelegramException($e->getMessage());
        }
    }

    /**
     * Replace the run in the database
     *
     * @param string $chat_id
     * @param string $run_timestamp
     * @param string $runned
     *
     * @return bool
     * @throws TelegramException
     */
    public static function replaceRun($chat_id, $run_timestamp, $runned)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sth = self::$pdo->prepare('REPLACE INTO `' . TB_RUN . '`
                (`chat_id`, `run_timestamp`, `runned`)
                VALUES
                (:chat_id, :run_timestamp, :runned)
            ');

            // $date = self::getTimestamp();

            $sth->bindValue(':chat_id', $chat_id);
            $sth->bindValue(':run_timestamp', $run_timestamp);
            $sth->bindValue(':runned', $runned);

            return $sth->execute();
        } catch (Exception $e) {
            throw new TelegramException($e->getMessage());
        }
    }

    /**
     * Update a specific run
     *
     * @param array $fields_values
     * @param array $where_fields_values
     *
     * @return bool
     * @throws TelegramException
     */
    public static function updateRun(array $fields_values, array $where_fields_values)
    {
        // Auto update the update_at field.
        $fields_values['updated_at'] = self::getTimestamp();

        return self::update(TB_RUN, $fields_values, $where_fields_values);
    }
}
