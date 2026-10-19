# the native PHP XAMPP project

using php 8.2, pdo - SQLite.

the project attracts Dictionary (im writing for learn English language).

project structure consists of the some folders:

1) public - frontend pages for using

2) controller - main layer which communicate with client query and puts up client data in service layer for server processing

3) service - basical kit of logic, which carry outs by routing in index.php - main starter file

4) data - keeps on the database and data for up-pasting

5) includes - the database connection and connection copy

6) model - structure, responsible for keep safety, reading and writing data to database

## API Endpoints

#### 1. Dictionary (Words)
Base URL: `http://localhost/MyProj/index.php/api/dictionary`

**GET** `/get` — *Get a paginated list of words*
- **Query Params:** 
  - `search` — search by word/translation
  - `sort` — sort order (`asc` or `desc`)
  - `page` — page number
- **Example URL:**
  ```text
  http://localhost/MyProj/index.php/api/dictionary/get?search=apple&sort=asc&page=1
  ```

**POST** `/create` — *Add new words*
- **Body (JSON):**
  ```json
  [
    {
      "word": "apple",
      "translation": "яблоко"
    }
  ]
  ```

**PUT** `/update` — *Update existing words*
- **Body (JSON):**
  ```json
  [
    {
      "id": "apple",
      "word": "apple",
      "translation": "яблочко"
    }
  ]
  ```
  > `id` is the old `eng_word`. If you change the word, pass the old one in `id` and the new one in `word`.

**DELETE** `/delete` — *Delete words*
- **Body (JSON):**
  ```json
  [
    {
      "id": "apple"
    }
  ]
  ```

---

#### 2. VerbForms (Verb forms)
Base URL: `http://localhost/MyProj/index.php/api/verb_forms`

**GET** `/get` — *Get a paginated list of verbs with translations*
- **Query Params:** `search`, `sort`, `page`
- **Example URL:**
  ```text
  http://localhost/MyProj/index.php/api/verb_forms/get?search=go&sort=desc&page=1
  ```
  > Response fields: `id`, `dictionary_id` (infinitive), `verb_2`, `verb_3`, `ru_word`.

**POST** `/create` — *Add a verb*
- **Body (JSON):**
  ```json
  [
    {
      "word": "go",
      "verb_2": "went",
      "verb_3": "gone",
      "translation": "идти"
    }
  ]
  ```
  > Automatically creates a record in the `Dictionary` table if it doesn't exist.

**PUT** `/update` — *Update verb forms*
- **Body (JSON):**
  ```json
  [
    {
      "id": "go",
      "word": "go",
      "verb_2": "went",
      "verb_3": "gone"
    }
  ]
  ```
  > `id` is the `dictionary_id` (infinitive). Translation is not updated here. Do not change the infinitive to avoid breaking the FOREIGN KEY constraint.

**DELETE** `/delete` — *Delete a verb*
- **Body (JSON):**
  ```json
  [
    {
      "id": "go"
    }
  ]
  ```
  > `id` is the `dictionary_id` (infinitive). Deletes only from `VerbForms`, the record in `Dictionary` remains.

---