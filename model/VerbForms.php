<?php

namespace model;

class VerbForms implements ModelInterface
{
    const LIMIT_PAGINATION = 50;

    //private int $id;
    private string $infinitive; // from Dictionary
    private string $verb_2;
    private string $verb_3;
    private string $ru_word; // from Dictionary

    private $pdo;

    public function __construct(string $infinitive, string $verb_2, string $verb_3, string $ru_word, $pdo)
    {
        $this->infinitive = $infinitive;
        $this->verb_2 = $verb_2;
        $this->verb_3 = $verb_3;
        $this->ru_word = $ru_word;

        $this->pdo = $pdo;

        $this->createTable();
    }

    public function findBy(array $criteria, int $page)
    {
        $offset = ($page - 1) * self::LIMIT_PAGINATION;

        $query = "SELECT vf.*, d.ru_word FROM VerbForms vf LEFT JOIN Dictionary d ON vf.dictionary_id = d.eng_word";

        $stmt = null;

        $sort_tail = "";

        if(!empty($criteria["sort"]))
        {
            switch ($criteria["sort"])
            {
                case 'asc':
                    $sort_tail = " ORDER BY vf.dictionary_id ASC";
                    break;
                case 'desc':
                    $sort_tail = " ORDER BY vf.dictionary_id DESC";
                    break;
            }
        }

        if(!empty($criteria["search"])){
            $stmt = $this->pdo->prepare($query . " WHERE (vf.dictionary_id LIKE ?) OR (vf.verb_2 LIKE ?) OR (vf.verb_3 LIKE ?) OR (d.ru_word LIKE ?)" . $sort_tail . " LIMIT ? OFFSET ?");
            //$stmt->execute(["%" . $criteria["search"] . "%", $this::LIMIT_PAGINATION, $offset]);

            // Bind the values obviously because in default behaviour PDO has translation in strings and not numeric types
            $stmt->bindValue(1, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(2, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(3, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(4, "%" . $criteria["search"] . "%", $this->pdo::PARAM_STR);
            $stmt->bindValue(5, self::LIMIT_PAGINATION, $this->pdo::PARAM_INT);
            $stmt->bindValue(6, $offset, $this->pdo::PARAM_INT);
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

    public function createTable(): void
    {
        // Create a table (SQLite will automatically create the database file here)
        $this->pdo->exec("PRAGMA foreign_keys = ON; " . "
            CREATE TABLE IF NOT EXISTS VerbForms (" .
                "id INTEGER PRIMARY KEY AUTOINCREMENT,
                dictionary_id TEXT,
                verb_2 TEXT NOT NULL,
                verb_3 TEXT NOT NULL,
                FOREIGN KEY (dictionary_id) REFERENCES Dictionary(eng_word)
            )
        ");
    }

    public function insert(): void
    {
        $dictionary = new Dictionary(
            $this->infinitive,
            $this->ru_word,
            $this->pdo
        );

        if (empty($dictionary->findOne(['eng_word' => $this->getInfinitive()]))) {
            //throw new \InvalidArgumentException('this word already exists!');

            $dictionary->insert();
        }

        // Insert a record
        $stmt = $this->pdo->prepare("INSERT INTO VerbForms (verb_2, verb_3, dictionary_id) VALUES (?, ?, ?)");
        $stmt->execute([$this->getVerb2(), $this->getVerb3(), $this->getInfinitive()]);
    }

    public function findOne(array $criteria): array
    {
        // find by key
        if (!isset($criteria['dictionary_id'])) {
            throw new \InvalidArgumentException(
                "findOne requires 'dictionary_id' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        $stmt = $this->pdo->prepare("SELECT * FROM VerbForms WHERE dictionary_id = ?");
        $stmt->execute([$criteria['dictionary_id']]);

        $row = $stmt->fetch();
        return $row === false ? [] : $row;
    }

    public function update(array $criteria): void
    {
        if (!isset($criteria['dictionary_id'])) {
            throw new \InvalidArgumentException(
                "update requires 'dictionary_id' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        $fields = [];
        $params = [
            ':old_dictionary_id' => $criteria['dictionary_id']
        ];

        if (!empty($this->getInfinitive())) {
            $fields[] = "dictionary_id = :dictionary_id";
            $params[':dictionary_id'] = $this->getInfinitive();
        }

        if (!empty($this->getVerb2())) {
            $fields[] = "verb_2 = :verb_2";
            $params[':verb_2'] = $this->getVerb2();
        }

        if (!empty($this->getVerb3())) {
            $fields[] = "verb_3 = :verb_3";
            $params[':verb_3'] = $this->getVerb3();
        }

        $stmt = $this->pdo->prepare(
            "UPDATE VerbForms
             SET " . implode(', ', $fields) .
            " WHERE dictionary_id = :old_dictionary_id"
        );

        $stmt->execute(
            $params
        );
    }

    public function delete(array $criteria): void
    {
        if (!isset($criteria['dictionary_id'])) {
            throw new \InvalidArgumentException(
                "delete requires 'dictionary_id' key, got: " . implode(', ', array_keys($criteria))
            );
        }

        // Prepairing SQL-query to delete by him infinitive
        $stmt = $this->pdo->prepare("DELETE FROM VerbForms WHERE dictionary_id = ?");

        // Execute the query, hands over the value of key in parameter array
        $stmt->execute([
            $criteria['dictionary_id']
        ]);
    }

    /**
     * @return string
     */
    public function getInfinitive(): string
    {
        return $this->infinitive;
    }

    /**
     * @return string
     */
    public function getVerb2(): string
    {
        return $this->verb_2;
    }

    /**
     * @return string
     */
    public function getVerb3(): string
    {
        return $this->verb_3;
    }

    /**
     * @return string
     */
    public function getRuWord(): string
    {
        return $this->ru_word;
    }
}