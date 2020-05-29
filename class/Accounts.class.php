<?php
class Accounts {
    private static $accounts;

    public static function pwd($username) {
        if (Accounts::$accounts == null) {
            $file = file_get_contents(Constant::ACCOUNT_FILE);
            Accounts::$accounts = json_decode($file);
        }
        foreach (Accounts::$accounts as $item) {
            if ($item->username == $username) {
                return $item->password;
            }
        }
        return null;
    }
}