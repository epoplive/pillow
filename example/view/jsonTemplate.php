<?php
use Zend\Json\Json;
?>
{
"success": <?= isset($success) == true ? "true" : "false"; ?>,
<?= isset($errors) ? '"errors": '. Json::encode($errors).',' : ""; ?>
<?= isset($messages) ? '"messages": '. Json::encode($messages).',' : ""; ?>
"result": <?= isset($result) ? Json::encode($result) : "{}"; ?>
}