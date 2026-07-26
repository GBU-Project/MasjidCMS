<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Load Domain Masjid Routes
if (file_exists(APPPATH . 'Domains/Masjid/Routes/masjid.php')) {
    require APPPATH . 'Domains/Masjid/Routes/masjid.php';
}

// Load Domain Jamaah Routes
if (file_exists(APPPATH . 'Domains/Jamaah/Routes/jamaah.php')) {
    require APPPATH . 'Domains/Jamaah/Routes/jamaah.php';
}
