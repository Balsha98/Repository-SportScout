<?php

require_once 'configuration.php';
require_once 'assets/classes/Router.php';
echo Router::renderPage($_GET['api'] ?? '/');
