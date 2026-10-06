<?php

namespace controller;

use model\Dictionary;

require_once 'model/Dictionary.php';

class DictionaryController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // POST
    public function insertWord(array $body): void
    {
        foreach ($body as $word) {
            $dictionary = new Dictionary(
                $word['word'],
                $word['translation'],
                $this->pdo
            );

            $dictionary->insert();
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["status" => "success", "message" => "Все слова успешно добавлены в базу данных SQLite!"]);
        exit;
    }
}