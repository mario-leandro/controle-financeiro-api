<?php

class Database {
    protected $db_connection = null;

    function __construct() {
        $this->db_connection = self::getDBConnection();
    }

    private function getDBConnection() {
        $this->db_connection = null;

        try {
            $this->db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->db_connection->exec("set names utf8");
        } catch(PDOException $e) {
            throw new Exception("Database connection error" . $e->getMessage());
            logMsg("Connection error: " . $e->getMessage());
        }

        return $this->db_connection;
    }

    public function isConnected() {
        return $this->db_connection !== null;
    }

    public function get($tabela, $condicao = "", $params = []) {
        try {
            $sql = "SELECT * FROM " . $tabela;
            if ($condicao != "") {
                $sql .= " WHERE " . $condicao;
            }

            $query = $this->db_connection->prepare($sql);
            $query->execute($params);

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Database get error: " . $e->getMessage();
            logMsg("Database get error: " . $e->getMessage());
            return false;
        }
    }

    public function get_limit($tabela, $condicao = "", $limit = 10) {
        try {
            $sql = "SELECT * FROM " . $tabela;
            if ($condicao != "") {
                $sql .= " WHERE " . $condicao;
            }
            $sql .= " LIMIT " . intval($limit);

            $query = $this->db_connection->prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Database get_limit error: " . $e->getMessage();
            logMsg("Database get_limit error: " . $e->getMessage());
            return false;
        }
    }

    public function insert($tabela, $dados) {
        try {
            $colunas = implode(", ", array_keys($dados));
            $placeholders = ":" . implode(", :", array_keys($dados));

            $sql = "INSERT INTO " . $tabela . " (" . $colunas . ") VALUES (" . $placeholders . ")";
            $query = $this->db_connection->prepare($sql);
            $query->execute($dados);

            return $this->db_connection->lastInsertId();
        } catch (Exception $e) {
            echo "Database insert error: " . $e->getMessage();
            logMsg("Database insert error: " . $e->getMessage());
            return false;
        }
    }

    public function update($tabela, $dados, $condicao, $condicaoParams = []) {
        try {
            $setClause = "";
            $setParams = [];
            foreach ($dados as $coluna => $valor) {
                $setClause .= $coluna . " = :set_" . $coluna . ", ";
                $setParams[":set_" . $coluna] = $valor;
            }
            $setClause = rtrim($setClause, ", ");

            $sql = "UPDATE " . $tabela . " SET " . $setClause . " WHERE " . $condicao;
            $query = $this->db_connection->prepare($sql);
            
            // Mesclar parâmetros do SET com parâmetros da condição WHERE
            $allParams = array_merge($setParams, $condicaoParams);
            $query->execute($allParams);

            return $query->rowCount();
        } catch (Exception $e) {
            echo "Database update error: " . $e->getMessage();
            logMsg("Database update error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($tabela, $condicao) {
        try {
            $sql = "DELETE FROM " . $tabela . " WHERE " . $condicao;
            $query = $this->db_connection->prepare($sql);
            $query->execute();

            return $query->rowCount();
        } catch (Exception $e) {
            echo "Database delete error: " . $e->getMessage();
            logMsg("Database delete error: " . $e->getMessage());
            return false;
        }
    }

    public function lastInsertId() {
        return $this->db_connection->lastInsertId();
    }
}