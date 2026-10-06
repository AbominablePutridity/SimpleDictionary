<?php

namespace model;

 interface ModelInterface
{
    public function createTable(): void;
    public function insert(): void;
    public function findOne(array $criteria): array;
    public function update(array $criteria, object $newData): void;
}