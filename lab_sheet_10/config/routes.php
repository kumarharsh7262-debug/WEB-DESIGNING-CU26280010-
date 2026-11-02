<?php
/** @var Router $router */

// ---- Web (HTML) ----
$router->get('/',                    'HomeController@index');
$router->get('/dashboard',           'HomeController@dashboard');

$router->get('/register',            'AuthController@registerForm');
$router->post('/register',           'AuthController@register');
$router->get('/login',               'AuthController@loginForm');
$router->post('/login',              'AuthController@login');
$router->post('/logout',             'AuthController@logout');
$router->get('/forgot-password',     'AuthController@forgotForm');
$router->post('/forgot-password',    'AuthController@forgot');
$router->get('/reset-password/{token}', 'AuthController@resetForm');
$router->post('/reset-password',     'AuthController@reset');

$router->get('/products',            'ProductController@index');
$router->get('/products/create',     'ProductController@create');
$router->post('/products',           'ProductController@store');
$router->get('/products/{id}/edit',  'ProductController@edit');
$router->post('/products/{id}/update','ProductController@update');
$router->post('/products/{id}/delete','ProductController@destroy');

$router->get('/uploads/{file}',      'UploadController@show');
$router->get('/api-client',          'HomeController@apiClient');

// ---- REST API (JSON, token auth) ----
$router->post('/api/login',          'ApiController@login');
$router->get('/api/products',        'ApiController@index');
$router->get('/api/products/{id}',   'ApiController@show');
$router->post('/api/products',       'ApiController@store');
$router->put('/api/products/{id}',   'ApiController@update');
$router->delete('/api/products/{id}','ApiController@destroy');
