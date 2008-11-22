<?php

if (!isset($argv[1]) or !isset($argv[2])) {
    echo "Usage: migrate.php <include path> <environment> \n";
    exit;
}

set_include_path($argv[1]);
define('ENVIRONMENT', $argv[2]);

require_once dirname(__FILE__) . '/../../application/bootstrap.php';

require_once APPLICATION_PATH . '/models/PhotoModel.php'; 
$photoModel = new PhotoModel;

foreach ($photoModel->fetchEntries() as $entry) {
    echo "Migrating [$entry->id] $entry->title \n" ;
    $photoModel->update($entry->toArray(), $entry->id);
}

