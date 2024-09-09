<?php

namespace App\Repositories;

interface DetteRepository
{
    /**
     * Crée une nouvelle dette.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Trouve une dette par son ID.
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * Met à jour une dette existante.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Supprime une dette par son ID.
     *
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * Recherche une dette par son ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    public function getAllDettes(array $filters); 
}
