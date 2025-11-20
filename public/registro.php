<?php
/**
 * Registro Page - Public Access Point
 */
session_start();
require_once __DIR__ . '/../config/env.php';

// Include the registro view
include __DIR__ . '/../app/Views/auth/registro.php';
