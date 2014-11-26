<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Simple chat</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function () {
                    $('input[type=submit]').attr('disabled', 'disabled');
                    $.post('index.php', {'message': $('form textarea').val()})
                        .done(function (data) {
                            location.href = 'index.php';
                        });
                return false;
            });
            $('form textarea').on('keyup', function(){
                if ($.trim($('form textarea').val())) {
                     $(this)[0].setCustomValidity('');
                } else {
                    $(this)[0].setCustomValidity('Enter your text message');
                }
            });
        });
    </script>
    <style>
        .ajax {
            color:#006400;
        }
    </style>
</head>
<body>
<?php

date_default_timezone_set('UTC');

require 'Database.php';
require 'User.php';
require 'Message.php';

$config = require 'config.php';
$db = new Database($config['db']);

$user = new User($config, $db);
$user->auth();

$message = new Message($config, $db);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['message'])) {
        $isXmlHttpRequest = false;
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $isXmlHttpRequest = true;
        }
        $message->saveMessage($user, $_POST['message'], $isXmlHttpRequest);
        header('location: index.php');
    }
}

$messagesArray = $message->getMessages();
?>
<form action="index.php" method="post">
    <textarea name="message"
              rows="4"
              cols="50"
              type="textarea"
              autofocus="autofocus"
              placeholder="Enter your text message"
              required>
    </textarea>
    <input type="submit" value="Send" />
</form>
<div>
    <ul class="messages">
        <?php foreach ($messagesArray as $num=>$msg): ?>
        <li <?=(!$msg['ajax_load'])?:'class="ajax"'; ?>>
            <span class="timestamp"><?=date_create($msg['created_at'])->format('H:i:s');?></span>
            <span class="name"><b><?=$msg['name']?></b></span>:
            <span class="message">
                <?=$num>=$config['messages']['amount_full_messages']?
                    $message->cutting($message->selecting($msg['message'])):
                    $message->selecting($msg['message'])?>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
