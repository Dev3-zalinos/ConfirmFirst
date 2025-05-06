<?php

return [
    'name' => 'Real Estate',
    'settings' => 'Settings',
    'login_form' => 'Login Form',
    'register_form' => 'Register Form',
    'forgot_password_form' => 'Forgot Password Form',
    'reset_password_form' => 'Reset Password Form',
    'consult_form' => 'Consult Form',
    'review_form' => 'Review Form',
    'theme_options' => [
        'slug_name' => 'Real Estate URLs',
        'slug_description' => 'Customize the slugs used for real estate pages. Be cautious when modifying as it can affect SEO and user experience. If something goes wrong, you can reset to default by typing the default value or leave it blank.',
        'page_slug_name' => ':page page slug',
        'page_slug_description' => 'It will look like :slug when you access the page. Default value is :default.',
        'page_slug_already_exists' => 'The :slug page slug is already in use. Please choose another one.',
        'page_slugs' => [
            'projects_city' => 'Projects by City',
            'projects_state' => 'Projects by State',
            'properties_city' => 'Properties by City',
            'properties_state' => 'Properties by State',
        ],
    ],
];
