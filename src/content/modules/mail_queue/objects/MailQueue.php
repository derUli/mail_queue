<?php
namespace MailQueue;

use Database;

class MailQueue
{

    private static $instance;

    private $currentMailId = 0;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllMails()
    {
        $mails = array();
        
        $query = Database::query("select id from `{prefix}mail_queue` order by id asc", true);
        
        while ($row = Database::fetchObject($query)) {
            $mails[] = new Mail($row->id);
        }
        return $mails;
    }

    public function getMailCount()
    {
        $query = Database::query("select count(id) as amount from `{prefix}mail_queue` order by id asc", true, true);
        $fetched = Database::fetchObject($query);
        return intval($fetched->amount);
    }

    public function getNextMail()
    {
        $query = Database::pQuery("select id from `{prefix}mail_queue` where id > ? order by id asc limit 1", array($this->currentMailId), true);
        if (Database::getNumRows($query) == 0) {
            return null;
        }
        
        $result = Database::fetchObject($query);
        $this->currentMailId = intval($result->id);
        return new Mail($result->id);
    }

    public function flushMailQueue()
    {
        Database::truncateTable("mail_queue");
    }

    public function addMail($mail)
    {
        $mail->save();
    }

    public function removeMail($mail)
    {
        $mail->delete();
    }
}
