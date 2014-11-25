<?php

class User {

    private $_id;

    private $_name;

    private $_session_id;

    private $_config;

    /**
     * Database handler
     */
    private $_db;

    /**
     * @param array $config
     * @param Database $db
     */
    public function __construct($config = [], $db)
    {
        $this->_config = $config;
        $this->_db = $db;
    }

    /**
     * Authorization
     *
     * trick: garbage collection mechanism does not guarantee removal sessions on time
     * @return void
     */
    public function auth()
    {
        $session_lifetime = $this->_config['user']['expired'];
        ini_set('session.use_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_lifetime', $session_lifetime);
        ini_set('session.gc_maxlifetime', $session_lifetime);
        session_start();
        $this->_session_id = session_id();

        $isSessionNotExists = !isset($_SESSION['LAST_ACTIVITY']);
        $isSessionExpired =  isset($_SESSION['LAST_ACTIVITY'])
            && (time() - $_SESSION['LAST_ACTIVITY'] > $session_lifetime);

        if ($isSessionNotExists) {
            $this->_saveNewUser();
        } elseif ($isSessionExpired) {
            session_unset(); // unset $_SESSION variable for the run-time
            session_destroy(); // destroy session data in storage
            session_start();  // new session
            $this->_session_id = session_id();
            $this->_saveNewUser();
        } else {
            $this->_loadUser();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return int
     */
    private function _saveNewUser()
    {
        $this->_name = $this->_generateName();

        $data = ['name' => $this->_name, 'session_id' => $this->_session_id];

        return $this->_db->preparedQuery('INSERT INTO users (name, session_id) VALUES (:name, :session_id)', $data);
    }

    /**
     * @return string
     */
    private function _generateName()
    {
          return $this->_config['user']['prefix'] . crc32($this->_session_id);
    }

    /**
     * @return void
     */
    private function _loadUser()
    {
        $sql = 'SELECT * FROM users WHERE session_id = :session_id';
        $data = ['session_id' => $this->_session_id];

        $userData = $this->_db->preparedQuery($sql, $data);

        $this->_id = $userData[0]['id'];
        $this->_name = $userData[0]['name'];
    }






}
