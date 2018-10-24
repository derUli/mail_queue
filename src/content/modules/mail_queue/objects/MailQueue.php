<?php
namespace MailQueue;

use Database;
use CMSConfig;

class MailQueue
{

    private static $instance;

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
        $query = Database::query("select id from `{prefix}mail_queue` order by id asc limit 1", true);
        if (Database::getNumRows($query) == 0) {
            return null;
        }
        
        $result = Database::fetchObject($query);
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

    // Delete all mails where max_tries is reached
    public function cleanUp()
    {
        $cfg = new CMSConfig();
        $mail_queue_max_tries = is_numeric($cfg->mail_queue_max_tries) ? intval($cfg->mail_queue_max_tries) : null;
        if (! is_null($mail_queue_max_tries)) {
            Database::pQuery("delete from {prefix}mail_queue where fails >= ?", array(
                $mail_queue_max_tries
            ), true);
        }
    }
}
