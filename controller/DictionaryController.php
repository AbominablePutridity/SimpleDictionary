<?php

namespace controller;

require_once __DIR__ . '/../service/DictionaryService.php';


class DictionaryController
{
    private string $domainAccess;
    private object $pdo;

    public function __construct(string $domainAccess, object $pdo)
    {
        $this->domainAccess = $domainAccess;
        $this->pdo = $pdo;

        $this->startController();
    }

    public function startController(): void
    {
        $request = new Request();

        // all endpoints
        $routes = [
            'dictionary_word_inserting' => $this->domainAccess . '/api/dictionary/create',
            'dictionary_word_updating' => $this->domainAccess . '/api/dictionary/update',
            'dictionary_word_deleting' => $this->domainAccess . '/api/dictionary/delete',
        ];

        // connecting services
        $dictionaryService = new DictionaryService($this->pdo);

        // routing logic
        if ($request->getMethod() === 'POST' &&
            $request->getRequestUri() === $routes['dictionary_word_inserting']) {

            $dictionaryService->insertWord(
                $request->decodeBody()
            );
        }

        if ($request->getMethod() === 'PUT' &&
            $request->getRequestUri() === $routes['dictionary_word_updating']) {

            $dictionaryService->updateWord(
                $request->decodeBody()
            );
        }

        if ($request->getMethod() === 'DELETE' &&
            $request->getRequestUri() === $routes['dictionary_word_deleting']) {

            $dictionaryService->deleteWord(
                $request->decodeBody()
            );
        }
    }
}