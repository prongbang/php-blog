<?php

namespace App\Model;

/**
 * Description of Authorization
 *
 * @author ex4
 */
class Authorization {

    /**
     * Check Session
     * @return boolean
     */
    public static function checkSession() {
        if (!empty($_SESSION['username'])) {

            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Get Session by Name
     * @param type $name
     * @return type
     */
    public static function getSession($name) {
        return !empty($_SESSION[$name]) ? $_SESSION[$name] : "";
    }

    /**
     * Push Session
     * @param type $name
     * @param type $value
     */
    public static function pushSession($name, $value) { 
        $_SESSION[$name] = $value;
    }

    /**
     * Unset Session
     * @param type $name
     */
     static function unsetSession($name) {
        session_unregister($name);
    }

    /**
     * Destroy Session
     */
    public static function destroySession() {
        session_destroy();
    }

}
