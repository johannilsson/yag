<?php

if (file_exists('../yag-conf.php')) {
    require '../yag-conf.php';
}

defined('BOOTSTRAP_PATH')
    or define('BOOTSTRAP_PATH', '../application/bootstrap_web.php');

require_once BOOTSTRAP_PATH;
