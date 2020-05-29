<?php
class User {
    public $username;
    public $password;
    private static $currentUser;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public static function setCurrentUser(User $user) {
        User::$currentUser = $user;
    } 

    public static function getCurrentUser():User {
        return User::$currentUser;
    }
}