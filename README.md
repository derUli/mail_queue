# mail_queue

If you send large amounts of mail in short time (for example: newslettters) you may risk to get blacklisted on mail blacklists.

This module provides a mail queue. Instead of sending your mails instantly,
you add them to the queue and they will be delivered with delay using a cronjob. This decreases the risk to false postive get blacklisted as a spammer.

mail_queue will send a limited amount of mails on every run of the cronjob.
After sending a mail it will be removed from queue.

**Please note**
This isn't a regular E-Mail Mode you have to customize existing modules so they will use the queue instead of regular mail delivery

## Requirements

* UliCMS 2018.3
* better_cron 1.0 or later

## Installation Howto

1. Open the configuration file `CMSConfig.php` in a text editor and add this two variable definitions under the last line starting with "var", but before the last "}"

```php
// runs a cronjob every x seconds
var $mail_queue_interval = 5 * 60;
// maximum amount of mails sent during an execution of the cronjob 
var $mail_queue_limit = 5;
```

You may adjust the options as required.

2. Install **[better_cron](https://extend.ulicms.de/better_cron.html)** package if not installed yet.

3. Install **mail_queue**.

## Code Example

```php
$mail_to = "john@doe.de";
$subject = "My Subject";
$header = "From: max@muster.de\r\n";
$header .= "Content-type: text/html; charset=utf-8";
$html = "<h1>Hello World</h1>";

$queue = \MailQueue\MailQueue::getInstance();
$mail = new \MailQueue\Mail();
$mail->setRecipient($mail_to);
$mail->setHeaders($header);
$mail->setSubject($subject);
$mail->setMessage($html);
$queue->addMail($mail);


```

## Admin Page

A user whose group has the permission `mail_queue_list` may show the mail queue in a table located under `Packages` > `mail_queue` > `Show`

## Unit Test

To run the unit test go to `ULICMS_ROOT` and run this command.

`vendor/bin/phpunit --bootstrap init.php content/modules/mail_queue/tests`

**Important Warning!**

Don't run the tests with a real mail server to avoid unnecessary spam.
Use a fake mail server instead.
I recommend [FakeSMTP](http://nilhcem.com/FakeSMTP/).


## Newsletter

The newsletter module in version 0.1.1 or later uses the queue instead of instant mail delivery if this module is installed.

## Upcoming Features

* A maximum count of delivery attempts. If more than X tries fails the mail will get deleted from queue.