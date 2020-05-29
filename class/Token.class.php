<?php
class Token {
    private static $salt = null;
    private static $currentToken = null;

    private static function getSalt() {
        if (Token::$salt === null) {
            Token::$salt = 56;
        }
        return Token::$salt;
    }

    public static function generateCurrentToken() {
        if (Token::$currentToken == null) {
            Token::$currentToken = Token::generateToken();
        }
        return Token::$currentToken;
    }

    public static function setCurrentToken($token) {
        Token::$currentToken = $token;
    }

    public static function generateToken() {
        if (($currentUser = User::getCurrentUser()) != null) {
            $body = new TokenBody();
            $body->u = $currentUser->username;
            $body->p = Token::encryptPwd($currentUser->password);
            $body->e = time() + 1 * 24 * 60 * 60;
            $json = json_encode($body);
            return Helper::base64url_encode(Token::xorSalt($json));
        }
        return null;
    }


    public static function verifyToken($token) {
        $json = Token::xorSalt(Helper::base64url_decode($token));
        $body = json_decode($json);
        $timeout = time() > $body->e;
        if (!$timeout) {
            $u = $body->u;
            $md5 = $body->p;
            $md51 = Token::encryptPwd(Accounts::pwd($u));
            return $md5 == $md51;
        }
        return false;
    }

    private static function xorSalt($str) {
        $len = strlen($str);
        $res = '';
        for ($i=0; $i < $len; $i++) { 
            $o = ord(substr($str, $i, 1)) ^ Token::getSalt();
            $c = chr($o);
            $res .= $c;
        }
        return $res;
    }

    private static function encryptPwd($pwd) {
        return md5($pwd xor Token::getSalt());
    }
}

class TokenBody {
    public $u;
    public $p;
    public $e;
}