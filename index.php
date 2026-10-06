<?php

use controller\Request;
use controller\DictionaryController;

require_once 'controller/Request.php';
require_once 'includes/DbConnection.php';
require_once 'controller/DictionaryController.php';


// Define the path to the database file
$dbPath = __DIR__ . '/data/database.sqlite';
$db = new DbConnection($dbPath);
$pdo = $db->getConnection();


$request = new Request();

const DOMAIN_ACCESS = '/MyProj/index.php'; // the access in the folder to project (my folder is 'MyProject consists in htdox - folder in XAMPP')

$routes = [
    'dictionary_word_inserting' => DOMAIN_ACCESS . '/api/dictionary/create'
];

if ($request->getMethod() === 'POST' &&
    $request->getRequestUri() === $routes['dictionary_word_inserting']) {

    $dictionaryController = new DictionaryController($pdo);
    $dictionaryController->insertWord(
        $request->decodeBody()
    );
}

?>
