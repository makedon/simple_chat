<?php

class Message {

    /**
     * Database handler
     */
    private $_db;

    private $_config;

    /**
     * @param array $config
     * @param Database $db
     */
    public function __construct($config, $db)
    {
        $this->_config = $config;
        $this->_db = $db;
    }

    /**
     * Escaping
     *
     * @param string $text
     * @return string
     */
    public function escape($text)
    {
        $filteredText = trim($text);
        $filteredText = strip_tags($filteredText);
        $filteredText = htmlentities($filteredText, ENT_QUOTES, 'UTF-8');

       return $filteredText;
    }

    /**
     * Cutting
     *
     * @param string $text
     * @return string
     */
    public function cutting($text)
    {
        $amount_chars =  $this->_config['messages']['amount_chars_old_messages'];
        return mb_substr($text, 0, $amount_chars, 'utf-8');
    }

    /**
     * Selecting
     *
     * @param string $text
     * @return string
     */
    public function selecting($text)
    {
        $pattern = "/(@[A-Za-z-_0-9]+)/i";
        $replacement = "<b>$1</b>";
        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * @param User $user
     * @param string $text
     * @param bool $isXmlHttpRequest
     * @return int
     */
    public function saveMessage($user, $text, $isXmlHttpRequest)
    {
        $data = [
            'user_id' => $user->getId(),
            'message' => $this->escape($text),
            'ajax_load' => (int) $isXmlHttpRequest,
        ];
        $sql = 'INSERT INTO messages (user_id, message, ajax_load) VALUES (:user_id, :message, :ajax_load)';

        return $this->_db->preparedQuery($sql, $data);
    }

    /**
     * Get messages (amount specified in the config)
     *
     * @return array
     */
    public function getMessages()
    {
        $data = ['limit' => $this->_config['messages']['total']];
        $sql = 'SELECT * FROM messages m INNER JOIN users u ON m.user_id = u.id ORDER BY created_at DESC LIMIT :limit';

        return $this->_db->preparedQuery($sql, $data);
    }
}
