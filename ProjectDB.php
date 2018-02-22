<?php

// namespace FakeNPC\FlRuTelegramBot;
// namespace Longman\TelegramBot;

//use Exception;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\DB;
//use PDO;

class ProjectDB extends DB
{
    /**
     * Initialize project table
     */
    public static function initializeProject()
    {
        if (!defined('TB_PROJECT')) {
            define('TB_PROJECT', self::$table_prefix . 'project');
        }
    }

    /**
     * Select a projects from the DB
     *
     * @param int   $id
     * @param int   $sended
     * @param int|null $limit
     *
     * @return array|bool
     * @throws TelegramException
     */
    public static function selectProject($id = null, $sended = null, $limit = null)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sql = '
              SELECT *
              FROM `' . TB_PROJECT . '`
            ';

            $where = array();

            if($id !== null) {
                if(is_array($id)) {
                    // 
                }
                else {
                    $where[] = '`id` = :id';
                }
            }

            if($sended !== null) {
                $where[] = '`sended` = :sended';
            }

            if(count($where)) {
                $sql .= ' WHERE '.join(' AND ', $where);
            }

            if ($limit !== null) {
                $sql .= ' LIMIT :limit';
            }

            $sth = self::$pdo->prepare($sql);

            if($id !== null) {
                $sth->bindValue(':id', $id, PDO::PARAM_INT);
            }

            if($sended !== null) {
                $sth->bindValue(':sended', $sended, PDO::PARAM_INT);

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
     * Insert the project in the database
     *
     * @param int $id
     * @param int $sended
     *
     * @return bool
     * @throws TelegramException
     */
    public static function insertProject($id, $sended)
    {
        if (!self::isDbConnected()) {
            return false;
        }

        try {
            $sth = self::$pdo->prepare('INSERT INTO `' . TB_PROJECT . '`
                (`id`, `sended`)
                VALUES
                (:id, :sended)
            ');

            // $date = self::getTimestamp();

            $sth->bindValue(':id', $id);
            $sth->bindValue(':sended', $sended);

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
    public static function updateProject(array $fields_values, array $where_fields_values)
    {
        return self::update(TB_PROJECT, $fields_values, $where_fields_values);
    }
}
