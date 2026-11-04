<?php

// Fix Vercel serverless SCRIPT_NAME and PHP_SELF so Symfony/Laravel doesn't treat /api as a base subdirectory
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Forward Vercel serverless requests to normal Laravel index.php
require __DIR__ . '/../public/index.php';
