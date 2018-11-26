<?php
namespace MailQueue;

use Database;
use Exception;
use Mailer;

class Mail extends \Model
{

    private $recipient;

    private $headers;

    private $subject;

    private $message;

    private $created;

    private $fails;

    // TODO: Make a constant for default priority
    private $priority = 128;

    public function loadByID($id)
    {
        $sql = "select * from `{prefix}mail_queue` where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVars($result);
        } else {
            throw new Exception("No mail with the id {$id}");
        }
    }

    public function fillVars($result = null)
    {
        if ($result) {
            $this->setID($result->id);
            $this->recipient = $result->recipient;
            $this->headers = $result->headers;
            $this->subject = $result->subject;
            $this->message = $result->message;
            $this->created = strtotime($result->created);
            $this->priority = intval($this->priority);
            $this->fails = $result->fails;
        } else {
            $this->setID(null);
            $this->recipient = null;
            $this->headers = null;
            $this->subject = null;
            $this->message = null;
            $this->created = null;
            $this->priority = 128;
            $this->fails = 0;
        }
    }

    public function insert()
    {
        $this->created = time();
        $sql = "insert into `{prefix}mail_queue` (recipient, headers, subject,
                message, created, priority) values (?, ?, ?, ?, from_unixtime(?), ?)";
        $args = array(
            $this->recipient,
            $this->headers,
            $this->subject,
            $this->message,
            $this->created,
            $this->priority
        );
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    public function update()
    {
        if ($this->getID()) {
            $sql = "update `{prefix}mail_queue` set recipient = ?, 
                     headers = ?, subject = ?, message = ?, created = ?,
                     fails = ?, priority = ? where id = ?";
            $args = array(
                $this->recipient,
                $this->headers,
                $this->subject,
                $this->message,
                date("Y-m-d H:i:s", $this->created),
                $this->fails,
                $this->priority,
                $this->getID()
            );
            Database::pQuery($sql, $args, true);
        }
    }

    public function delete()
    {
        if ($this->getID()) {
            Database::pQuery("delete from `{prefix}mail_queue` where id = ?", array(
                $this->getID()
            ), true);
            $this->fillVars(null);
        }
    }

    public function send()
    {
        if (Mailer::send($this->recipient, $this->subject, $this->message, $this->headers)) {
            $this->delete();
            return true;
        }
        $this->fails += 1;
        $this->save();
        return false;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getFails()
    {
        return $this->fails;
    }

    public function setFails($val)
    {
        $this->fails = intval($val);
    }

    public function setRecipient($val)
    {
        $this->recipient = ! is_null($val) ? strval($val) : null;
    }

    public function setHeaders($val)
    {
        $this->headers = ! is_null($val) ? strval($val) : null;
    }

    public function setSubject($val)
    {
        $this->subject = ! is_null($val) ? strval($val) : null;
    }

    public function setMessage($val)
    {
        $this->message = ! is_null($val) ? strval($val) : null;
    }

    public function setCreated($val)
    {
        $this->created = ! is_null($val) ? intval($val) : null;
    }
    public function getPriority(){
        return $this->priority;
    }
    public function setPriority($val){
        if(!is_numeric($val)){
            throw new InvalidArgumentException();
        }
        $this->priority = intval($val);
    }
}
