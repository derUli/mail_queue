# mail_queue

If you send large amounts of mail in short time (for example: newslettters) you may risk to get blacklisted on mail blacklists.

This module provides a mail queue. Instead of sending your mails instantly,
you add them to the queue and they will be delivered with delay using a cronjob. This decreases the risk to false postive get blacklisted as a spammer.

mail_queue will send a limited amount of mails on every run of the cronjob.
After sending a mail it will be removed from queue.

## Requirements

* UliCMS 2018.3.1
* better_cron 1.0 or later

## Installation Howto

...

## Code Example

```php
// Use mail_queue module for mail delivery if installed
		// https://github.com/derUli/mail_queue
		$mail_to = "john@doe.de";
		$subject = "My Subject";
		$header = "From: max@muster.de\r\n";
		$header .= "Content-type: text/html; charset=utf-8";
		$html = "<h1>Hello World</h1>";
		
        if (class_exists('\MailQueue\MailQueue')) {
            $queue = \MailQueue\MailQueue::getInstance();
            $mail = new \MailQueue\Mail();
            $mail->setRecipient($mail_to);
            $mail->setHeaders($header);
            $mail->setSubject($subject);
            $mail->setMessage($html);
            $queue->addMail($mail);
            return true;
        }

```

## Admin Page

...

## Upcoming Features

...