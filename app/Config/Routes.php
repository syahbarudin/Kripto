<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'FileEncryptor::index'); // Menampilkan daftar file
$routes->get('/auth/login', 'Auth::login');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/create', 'Auth::create');
$routes->post('/auth/check', 'Auth::check');
$routes->get('/auth/logout', 'Auth::logout');

// Rute untuk pengelolaan file

$routes->post('/FileEncryptor/upload', 'FileEncryptor::upload'); // Mengupload file
$routes->get('/FileEncryptor/download/(:num)', 'FileEncryptor::download/$1'); // Mengunduh file
$routes->post('/FileEncryptor/downloadWithKey', 'FileEncryptor::downloadWithKey'); // Mengunduh file dengan kata kunci
$routes->get('/FileEncryptor/delete/(:num)', 'FileEncryptor::delete/$1'); // Menghapus file
