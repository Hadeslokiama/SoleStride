<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

/**
 * Middleware. Include at the top of any page requiring authentication.
 * Buyer pages: require_login().
 * Admin pages: require_admin().
 * Call is left to the including file, not forced here, since some
 * pages (index.php, product-details.php) are public.
 */
start_secure_session();