<?php

return [
    'language'           => 'English',
    'unknown'            => 'Unknown error',
    'success'            => 'Success',
    'redirect'           => 'Redirect',
    'unauthorized'       => 'Unauthorized',
    'forbidden'          => 'Forbidden',
    // 后台相关
    'admin'              => [
        'login' => [
            'key1'  => 'Username',
            'key2'  => 'Password',
            'key3'  => 'or sign in with',
            'key4'  => 'Google',
            'key5'  => 'Facebook',
            'key6'  => 'Remeber this Device',
            'key7'  => 'Sign In',
            'key8'  => 'Username required',
            'key9'  => 'Login password is required',
            'key10' => 'Verification Code',
            'key11' => 'Image verification code is required.',
        ]
    ],
    // 错误提示
    'business_exception' => [
        'key1' => 'Too many login attempts failed. Please try again later.',
        'key2' => 'Image verification code error',
    ],
    'validator'          => [
        'admin' => [
            'key1' => 'Usernames can only contain letters and numbers',
            'key2' => 'Username cannot contain spaces',
            'key3' => 'Username must be between 4 and 18 characters long',
            'key4' => 'Please enter a valid email address',
            'key5' => 'Password must be a string',
            'key6' => 'Password must be between 6 and 32 characters long',
        ]
    ]
];