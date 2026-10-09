<?php

use controller\DictionaryController;
use controller\DictionaryService;
use controller\Request;

require_once 'controller/Request.php';
require_once 'includes/DbConnection.php';
require_once 'controller/DictionaryController.php';


// Define the path to the database file
$dbPath = __DIR__ . '/data/database.sqlite';
$db = new DbConnection($dbPath);
$pdo = $db->getConnection();


// main domain for routing
const DOMAIN_ACCESS = '/MyProj/index.php'; // the access in the folder to project (my folder is 'MyProject consists in htdox - folder in XAMPP')

$dictionaryController = new DictionaryController(DOMAIN_ACCESS, $pdo);

?>
