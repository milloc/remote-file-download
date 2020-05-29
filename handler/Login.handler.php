<?php
Helper::registryFilter("/", function () {
    if (($uri = $_SERVER['REQUEST_URI']) == '/toLogin' || $uri == '/login') {
        return true;
    }

    $token = $_SERVER['HTTP_TOKEN'] ?? (Helper::request('token') ?: null);
    if ($token !== null) {
        if (Token::verifyToken($token)) {
            Token::setCurrentToken($token);
            return true;
        }
    }

    header('Location: /toLogin');
    exit;
});

Helper::registry("/toLogin", function () {
    return 'toLogin';
});

Helper::registry('/login', function () {
    $username = Helper::request('username');
    $password = Helper::request('password');
    $pwd = Accounts::pwd($username);
    if ($pwd == null || $pwd !== $password) {
        Helper::addViewAttribute('err', '密码错误!');
        return '/toLogin';
    }
    User::setCurrentUser(new User($username, $pwd));
    header('location: file?token=' . Token::generateToken());
});