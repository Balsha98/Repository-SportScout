<?php

require_once '../class/Session.php';
require_once '../class/Cookie.php';
require_once '../class/Redirect.php';

Session::commence();

// Kill session.
Session::logout();

// Let cookies expire.
Cookie::unset_all();

// Redirect user to login page.
Redirect::redirect_to('login');
