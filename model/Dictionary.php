<?php

namespace model;

use http\Exception\InvalidArgumentException;

require_once 'model/ModelInterface.php';

class Dictionary implements ModelInterface
{
    const LIMIT_PAGINATION = 50;
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
                "eng_word TEXT PRIMARY KEY NOT NULL UNIQUE,
                ru_word TEXT NOT NULL
            )
        ");

    }

    /**
     * find all recordings in a dictionary and find data at criteria and pagination
     * @param array $criteria
     * @param int $page
     * @return array
     */
    function findBy(array $criteria, int $page): array
    {
        $offset = ($page - 1) * self::LIMIT_PAGINATION;

        $query = "SELECT * FROM Dictionary";

        $stmt = null;

        $sort_tail = "";

        if(!empty($criteria["sort"]))
        {
            switch ($criteria["sort"])
            {
                case 'asc':
                    $sort_tail = " ORDER BY eng_word ASC";
                    break;
                case 'desc':
                    $sort_tail = " ORDER BY eng_word DESC";
                    break;
            }
        }

        if(!empty($criteria["search"])){
            $stmt = $this->pdo->prepare($query . " WHERE (eng_word LIKE ?) OR (ru_word LIKE ?)" . $sort_tail . " LIMIT ? OFFSET ?");
            //$stmt->execute(["%" . $criteria["search"] . "%", $this::LIMIT_PAGINATION, $offset]);

            // Bind the values obviously because in default behaviour PDO has translation in strings and not numeric types
            $stmt->bindValue(1, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(2, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(3, self::LIMIT_PAGINATION, $this->pdo::PARAM_INT);
            $stmt->bindValue(4, $offset, $this->pdo::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare($query . $sort_tail . " LIMIT ? OFFSET ?");
            //$stmt->execute([$this::LIMIT_PAGINATION, $offset]);

            // Bind the values obviously because in default behaviour PDO has translation in strings and not numeric types
            $stmt->bindValue(1, self::LIMIT_PAGINATION, $this->pdo::PARAM_INT);
            $stmt->bindValue(2, $offset, $this->pdo::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll($this->pdo::FETCH_ASSOC);
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
    public function update(array $criteria): void
    {
        if (!isset($criteria['eng_word'])) {
            throw new \InvalidArgumentException(
                "update requires 'eng_word' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        $fields = [];
        $params = [
            ':old_eng' => $criteria['eng_word']
        ];

        if (!empty($this->getEngWord())) {
            $fields[] = "eng_word = :new_eng";
            $params[':new_eng'] = $this->getEngWord();
        }

        if (!empty($this->getRuWord())) {
            $fields[] = "ru_word = :new_ru";
            $params[':new_ru'] = $this->getRuWord();
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

    public function delete(array $criteria): void
    {
        if (!isset($criteria['eng_word'])) {
            throw new \InvalidArgumentException(
                "delete requires 'eng_word' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        // Prepairing SQL-query to delete
        $stmt = $this->pdo->prepare("DELETE FROM Dictionary WHERE eng_word = ?");

        // Execute the query
        $stmt->execute([
            $criteria['eng_word']
        ]);
    }

    public function getEngWord(): string { return $this->eng_word; }
    public function getRuWord(): string  { return $this->ru_word;  }
}