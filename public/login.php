<?php
/**
 * Login Page - Public Access Point
 */
session_start();
require_once __DIR__ . '/../config/env.php';

// Include the login view
include __DIR__ . '/../app/Views/auth/login.php';
