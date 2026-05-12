<?php
define('ROOT', __DIR__);
require_once ROOT . '/config/database.php';
require_once ROOT . '/core/App.php';

$db = App::db();
$stmt = $db->query('SELECT id, name, avatar FROM users WHERE id IN (4, 7)');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Users:\n";
print_r($users);

$stmt = $db->query('SELECT a.id as app_id, a.user_id, u.name, u.avatar FROM applications a JOIN users u ON u.id = a.user_id WHERE a.id = 9');
$app = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\nApplication 9:\n";
print_r($app);