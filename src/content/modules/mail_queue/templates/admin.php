<?php
use MailQueue\MailQueue as MailQueue;
$queue = MailQueue::getInstance();

$mails = $queue->getAllMails();
?>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("id");?></th>
			<th><?php translate("recipient");?></th>
			<th><?php translate("headers");?></th>
			<th><?php translate("subject");?></th>
			<th><?php translate("message");?></th>
			<th><?php translate("created");?></th>
		</tr>
	</thead>
	<tbody>
<?php

foreach ($mails as $mail) {
    ?>
<tr>
			<td><?php esc($mail->getId());?></td>
			<td><?php esc($mail->getRecipient());?></td>
			<td><?php echo nl2br(_esc($mail->getHeaders()));?></td>
			<td><?php esc($mail->getSubject());?></td>
			<td><?php echo nl2br(_esc($mail->getMessage()));?></td>
			<td><?php echo _esc(date('Y-m-d H:i:s', $mail->getCreated()));?></td>
		</tr>
    <?php
}
?>
</tbody>
</table>