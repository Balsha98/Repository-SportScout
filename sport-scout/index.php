<?php

require_once 'configuration.php';
require_once 'assets/class/Router.php';
echo Router::renderPage($_GET['request'] ?? '/');
