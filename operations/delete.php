<?php
require_once "../include/db.php";
$db = new db;
$db->query("UPDATE `campaign` SET `deleted`='1' WHERE `id`='{$_POST['id']}'");
$db->commit();
?>