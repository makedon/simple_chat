<?php

class Database {

    /**
     * Database handler
     */
    private $_pdo = null;

    /**
     * Construct for this class
     *
     * @param array $config configuration array
     * @result void
     * @throw PDOException
     */
    public function __construct($config = [])
    {
        try {
            if (!isset($config['options'])) {
                $config['options'] = null;
            }
            $this->_pdo = new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->_pdo->exec("SET time_zone='+0:00';");
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    /**
     * Send RAW query to DB
     *
     * @param string $query raw query to db
     * @param array $params params for query
     * @return PDOStatement
     */
    public function rawQuery($query, $params = [])
    {
        try {
            $st = $this->_pdo->query($query);
            $st->execute($params);
        } catch (PDOException $e) {
            echo "Query error: " . $e->getMessage();
        }
        return $st;
    }

    /**
     * Send prepared query to DB
     *
     * @param string $query
     * @param array $params params for prepared query
     * @return PDOStatement|array|int
     */
    public function preparedQuery($query, $params = [])
    {
        try {
            $st = $this->_pdo->prepare($query);
            $st->execute($params);
        } catch (PDOException $e) {
            echo "Query error: " . $e->getMessage();
        }

        $rawStatement = explode(' ', $query);
        $statement = strtoupper($rawStatement[0]);

        if ($statement === 'SELECT' || $statement === 'SHOW') {
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } elseif ( $statement === 'INSERT' || $statement === 'UPDATE' || $statement === 'DELETE' ) {
            return $st->rowCount();
        }
    }
}
