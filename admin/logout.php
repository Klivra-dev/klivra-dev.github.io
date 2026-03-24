<?php
require_once __DIR__ . '/../includes/config.php';
startAdminSession();
session_destroy();
header('Location: login.php');
exit;
