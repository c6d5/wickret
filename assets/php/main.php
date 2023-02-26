<?php
require_once('./TelegramNotifier.php');

$notifier = new TelegramNotifier('5384183489:AAE71ZXpckXsOXArEHN7Ib4lXCw4juRL4Tg', '-790883384');
$notifier->sendNotification();
?>
