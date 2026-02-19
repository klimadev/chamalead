<?php
/**
 * Logout Handler
 *
 * Destroys user session securely and redirects to login page.
 *
 * @package Panel
 * @author Chamalead
 * @version 2.0.0
 */

require_once 'auth.php';

// Use centralized logout function
logout();

// Redirect to login
header("Location: login.php");
exit;
