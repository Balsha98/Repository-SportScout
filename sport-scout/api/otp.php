<?php

require_once '../assets/class/Session.php';
require_once '../assets/class/Database.php';
require_once '../assets/class/Encoder.php';

Session::commence();
$input = Encoder::fromJSON(file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
}
