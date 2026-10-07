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

// main domain for routing
const DOMAIN_ACCESS = '/MyProj/index.php'; // the access in the folder to project (my folder is 'MyProject consists in htdox - folder in XAMPP')

// all endpoints
$routes = [
    'dictionary_word_inserting' => DOMAIN_ACCESS . '/api/dictionary/create',
    'dictionary_word_updating' => DOMAIN_ACCESS . '/api/dictionary/update',
    'dictionary_word_deleting' => DOMAIN_ACCESS . '/api/dictionary/delete',
];

// connecting controllers
$dictionaryController = new DictionaryController($pdo);

// routing logic
if ($request->getMethod() === 'POST' &&
    $request->getRequestUri() === $routes['dictionary_word_inserting']) {

    $dictionaryController->insertWord(
        $request->decodeBody()
    );
}

if ($request->getMethod() === 'PUT' &&
    $request->getRequestUri() === $routes['dictionary_word_updating']) {

    $dictionaryController->updateWord(
        $request->decodeBody()
    );
}

if ($request->getMethod() === 'DELETE' &&
    $request->getRequestUri() === $routes['dictionary_word_deleting']) {

    $dictionaryController->deleteWord(
        $request->decodeBody()
    );
}

?>
