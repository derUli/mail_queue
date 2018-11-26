<?php
include_once "templating.php";

class MailQueueTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $queue = MailQueue\MailQueue::getInstance();
        $queue->flushMailQueue();
    }

    public function testSendMails()
    {
        $queue = MailQueue\MailQueue::getInstance();
        for ($i = 1; $i <= 100; $i ++) {
            $mail = new MailQueue\Mail();
            $mail->setRecipient("receiver{$i}@example.org");
            $mail->setSubject("Subject $i");
            $mail->setMessage("Message $i");
            $mail->setHeaders("From: foo@bar.de");
            $queue->addMail($mail);
        }
        $mails = $queue->getAllMails();
        $this->assertEquals(100, count($mails));
        $this->assertEquals(100, $queue->getMailCount());
        
        $mail3 = $mails[2];
        $this->assertEquals("Subject 3", $mail3->getSubject());
        
        $mail97 = $mails[96];
        $this->assertEquals("Subject 97", $mail97->getSubject());
        for ($i = 100; $i > 0; $i --) {
            $nextMail = $queue->getNextMail();
            $this->assertNotNull($nextMail);
            $this->assertTrue($nextMail->send());
            $this->assertEquals($i - 1, count($queue->getAllMails()));
        }
        
        $queue->flushMailQueue();
    }

    // This test case was written because there was a bug that caused
    // getNextMail() always returned the first mail in the queue.
    // the mail queue stopped working if the delivery of the first mail in queue always fails
    public function testGetNextMail()
    {
        $queue = MailQueue\MailQueue::getInstance();
        $mailIds = array();
        for ($i = 1; $i <= 3; $i ++) {
            $mail = new MailQueue\Mail();
            $mail->setRecipient("receiver{$i}@example.org");
            $mail->setSubject("Subject $i");
            $mail->setMessage("Message $i");
            $mail->setHeaders("From: foo@bar.de");
            $queue->addMail($mail);
            $mailIds[] = $mail->getID();
        }
        
        $mail = $queue->getNextMail();
        $this->assertEquals($mailIds[0], $mail->getID());
        $mail = $queue->getNextMail();
        $this->assertEquals($mailIds[1], $mail->getID());
        $mail = $queue->getNextMail();
        $this->assertEquals($mailIds[2], $mail->getID());
        
        $queue->flushMailQueue();
    }

    // add a mail with unicode subject and message to the queue and read it.
    // This is a test case for a bug fix which was implemented in mail_queue version 1.4.
    public function testAddMailWithUnicode(){
        throw new NotImplementedException();
    }
}