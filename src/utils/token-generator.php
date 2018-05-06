<?php

class TokenGenerator {

    public static function generate() {
        // set secret to setting
        $secret_key = "secretkey";
        return TokenGenerator::generate_hash_ip().'.'.base64_encode(md5(time().$secret_key)).'.'.md5('hello');
    }

    public static function generate_hash_ip() { 
        $ip = IPUtil::get_ip_address();
        return base64_encode(sha1($ip));
    }

    public static function generate_by_time() { 
        return ((time() + rand(10,100000)).":". time().":".(time() + rand(10,1000)).":".(time() + rand(19999,199900)));
    }

    public static function get_hash_ip($token) {
        $token = explode(".", $token);
        if(count($token) > 0){
            return $token[0];
        } else {
            return "";
        }
    }

    public static function authen($request) { 

        $ip = IPUtil::get_ip_address();
        $validate = IPUtil::validate_ip($ip);
        $auth = $request->getHeader('Authorization');
        
        // อาจจะใช้เวลาในการเช็ค

        // TODO true for dev
        // if(!$validate) $validate = true;
        
        if($validate && count($auth) > 0) {
            $token = TokenGenerator::generate_hash_ip();
            $auth = str_replace("Bearer ", '', $auth[0]);
            if (TokenGenerator::get_hash_ip($auth) == $token) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function setCookie($token) {
        // 1 Day expired
        setcookie('_hfjeo', $token, time() + (86400 * 1), "/");
    }

    public static function checkCookie() {
        if(!isset($_COOKIE['_hfjeo'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}