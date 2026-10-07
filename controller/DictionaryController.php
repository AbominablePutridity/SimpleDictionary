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
    //[
    //  {eng, ru},
    //  {eng, ru},
    //]
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
        echo json_encode(["status" => "success", "message" => "all words successfly added to SQLite database!"]);
        exit;
    }

    // PUT
    //[
    //  {id, eng, ru},
    //  {id, eng, ru},
    //]
    public function updateWord(array $body): void
    {
        foreach ($body as $word) {
            $criteria = $word['id'];

            $dictionary = new Dictionary(
                $word['word'],
                $word['translation'],
                $this->pdo
            );

            $dictionary->update(['eng_word' => $criteria]);
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["status" => "success", "message" => "all words successfly updated to SQLite database!"]);
        exit;
    }

    // DELETE
    //[
    //  {id},
    //  {id}
    //]
    public function deleteWord(array $body): void
    {
        foreach ($body as $word) {
            $criteria = $word['id'];

            $dictionary = new Dictionary(
                "",
                "",
                $this->pdo
            );

            $dictionary->delete(['eng_word' => $criteria]);
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["status" => "success", "message" => "all words successfly deleted to SQLite database!"]);
        exit;
    }
}