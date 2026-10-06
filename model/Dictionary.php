<?php

namespace model;

use http\Exception\InvalidArgumentException;

require_once 'model/ModelInterface.php';

class Dictionary implements ModelInterface
{
    private string $eng_word;
    private string $ru_word;

    private $pdo;

    public function __construct(string $word, string $ru_word, $pdo)
    {
        $this->eng_word = $word;
        $this->ru_word = $ru_word;

        $this->pdo = $pdo;

        $this->createTable();
    }

    /**
     * create table in db.
     * @return void
     */
    public function createTable(): void
    {
        // Create a table (SQLite will automatically create the database file here)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS Dictionary (" .
                //id INTEGER PRIMARY KEY AUTOINCREMENT,
                "eng_word TEXT NOT NULL UNIQUE,
                ru_word TEXT NOT NULL
            )
        ");

    }

    /**
     * find entity by criteria params.
     * @param array $criteria param for searching.
     * @return array searched entity.
     */
    public function findOne(array $criteria): array
    {
        // find by key
        if (!isset($criteria['eng_word'])) {
            throw new \InvalidArgumentException(
                "findOne requires 'eng_word' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        $stmt = $this->pdo->prepare("SELECT * FROM Dictionary WHERE eng_word = ?");
        $stmt->execute([$criteria['eng_word']]);

        $row = $stmt->fetch();
        return $row === false ? [] : $row;
    }

    /**
     * Inserting data into entity.
     * @param object $entity object to insert.
     * @return void
     */
    public function insert(): void
    {
        if (!empty($this->findOne(['eng_word' => $this->getEngWord()]))) {
            throw new \InvalidArgumentException('this word already exists!');
        }

        // Insert a record
        $stmt = $this->pdo->prepare("INSERT INTO Dictionary (eng_word, ru_word) VALUES (?, ?)");
        $stmt->execute([$this->getEngWord(), $this->getRuWord()]);
    }

    /**
     * Update data by criteria param.
     * @param array $criteria param for updating/
     * @param object $newData new data for update
     * @return void
     */
    public function update(array $criteria, object $newData): void
    {
        if(!$newData instanceof Dictionary) {
            throw new \InvalidArgumentException('Not a Dictionary');
        }

        if (!isset($criteria['eng_word'])) {
            throw new \InvalidArgumentException(
                "update requires 'eng_word' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        $fields = [];
        $params = [
            ':old_eng' => $criteria['eng_word']
        ];

        if (!empty($newData->getEngWord())) {
            $fields[] = "eng_word = :new_eng";
            $params[':new_eng'] = $newData->getEngWord();
        }

        if (!empty($newData->getRuWord())) {
            $fields[] = "ru_word = :new_ru";
            $params[':new_ru'] = $newData->getRuWord();
        }

        $stmt = $this->pdo->prepare(
            "UPDATE Dictionary
             SET " . implode(', ', $fields) .
             " WHERE eng_word = :old_eng"
        );

        $stmt->execute(
            $params
        );
    }

    public function getEngWord(): string { return $this->eng_word; }
    public function getRuWord(): string  { return $this->ru_word;  }
}