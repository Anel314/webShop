<?php

class BaseService
{
    protected $dao;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }
    public function query($query, $params)
    {

        try {
            return $this->dao->query($query, $params);
        } catch (Exception $e) {
            throw new Exception("Query error: " . $e->getMessage());
        }
    }
    public function query_unique($query, $params)
    {
        try {
            $result = $this->dao->query_unique($query, $params);
            return $result ?: null;
        } catch (Exception $e) {
            throw new Exception("Query unique error: " . $e->getMessage());
        }
    }
    public function getAll()
    {
        try {
            return $this->dao->getAll();
        } catch (Exception $e) {
            throw new Exception("Failed to get all records: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        if (empty($id)) {
            throw new Exception("ID cannot be empty");
        }

        try {
            $result = $this->dao->getById($id);
            return $result ?: null;
        } catch (Exception $e) {
            throw new Exception("Failed to get record by ID: " . $e->getMessage());
        }
    }

    public function add($entity)
    {
        try {
            return $this->dao->add($entity);
        } catch (Exception $e) {
            throw new Exception("Failed to add record: " . $e->getMessage());
        }
    }

    public function update($entity, $id, $id_column = "id")
    {
        if (empty($id)) {
            throw new Exception("ID cannot be empty");
        }
        try {
            return $this->dao->update($entity, $id, $id_column);
        } catch (Exception $e) {
            throw new Exception("Failed to update record: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (empty($id)) {
            throw new Exception("ID cannot be empty");
        }

        try {
            return $this->dao->delete($id);
        } catch (Exception $e) {
            throw new Exception("Failed to delete record: " . $e->getMessage());
        }
    }
}
?>