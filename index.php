<?php

use controller\DictionaryController;
use controller\DictionaryService;

use controller\VerbFormsController;
use controller\VerbFormsService;

use controller\Request;

require_once 'controller/Request.php';
require_once 'includes/DbConnection.php';
require_once 'controller/DictionaryController.php';
require_once 'controller/VerbFormsController.php';


// Define the path to the database file
$dbPath = __DIR__ . '/data/database.sqlite';
$db = new DbConnection($dbPath);
$pdo = $db->getConnection();


// main domain for routing
const DOMAIN_ACCESS = '/MyProj/index.php'; // the access in the folder to project (my folder is 'MyProject consists in htdox - folder in XAMPP')

// Starting controllers
$dictionaryController = new DictionaryController(DOMAIN_ACCESS, $pdo);
$verbFormsController = new VerbFormsController(DOMAIN_ACCESS, $pdo);

?>
