<?php

namespace controller;

require_once __DIR__ . '/../service/VerbFormsService.php';

class VerbFormsController
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
            'verb_forms_get' => $this->domainAccess . '/api/verb_forms/get',
            'verb_forms_inserting' => $this->domainAccess . '/api/verb_forms/create',
            'verb_forms_updating' => $this->domainAccess . '/api/verb_forms/update',
            'verb_forms_deleting' => $this->domainAccess . '/api/verb_forms/delete',
        ];

        // connecting services
        $verbFormsService = new VerbFormsService($this->pdo);

        // routing logic
        if ($request->getMethod() === 'GET' &&
            $request->getRequestUri() === $routes['verb_forms_get']) {

            $verbFormsService->getVerbForms(
                $request->getQueryParams()
            );
        }

        if ($request->getMethod() === 'POST' &&
            $request->getRequestUri() === $routes['verb_forms_inserting']) {

            $verbFormsService->insertWord(
                $request->decodeBody()
            );
        }

        if ($request->getMethod() === 'PUT' &&
            $request->getRequestUri() === $routes['verb_forms_updating']) {

            $verbFormsService->updateWord(
                $request->decodeBody()
            );
        }

        if ($request->getMethod() === 'DELETE' &&
            $request->getRequestUri() === $routes['verb_forms_deleting']) {

            $verbFormsService->deleteWord(
                $request->decodeBody()
            );
        }
    }
}