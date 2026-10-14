<?php

namespace controller;

use model\VerbForms;

require_once 'model/VerbForms.php';

class VerbFormsService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // GET {search, page, sort}
    public function getVerbForms(array $params)
    {
        $verbForms = new VerbForms(
            "",
            "",
            "",
            "",
            $this->pdo
        );

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(
            $verbForms->findBy(
                [
                    "search" => $params["search"],
                    "sort" => $params["sort"]
                ],
                $params["page"]
            )
        );
        exit;
    }

    // POST
    //[
    //  {eng, ru},
    //  {eng, ru},
    //]
    public function insertWord(array $body): void
    {
        foreach ($body as $word) {
            $verbForms = new VerbForms(
                $word['word'],
                $word["verb_2"],
                $word["verb_3"],
                $word['translation'],
                $this->pdo
            );

            $verbForms->insert();
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

            $verbForms = new VerbForms(
                $word['word'],
                $word["verb_2"],
                $word["verb_3"],
                $word['translation'],
                $this->pdo
            );

            $verbForms->update(['dictionary_id' => $criteria]);
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

            $verbForms = new VerbForms(
                "",
                "",
                "",
                "",
                $this->pdo
            );

            $verbForms->delete(['eng_word' => $criteria]);
        }

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["status" => "success", "message" => "all words successfly deleted to SQLite database!"]);
        exit;
    }
}