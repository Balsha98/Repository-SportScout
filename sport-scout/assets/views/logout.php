<?php

Session::commence();

// Kill session.
Session::logout();

// Let cookies expire.
Cookie::unsetAllCookies();

// Redirect user to login page.
Redirect::toPage('login');
