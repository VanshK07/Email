<?php
// cron.php - Handles sending GitHub updates via email

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the functions
require_once __DIR__ . '/functions.php';

// Log the cron execution
$logFile = __DIR__ . '/cron.log';
$timestamp = date('Y-m-d H:i:s');

try {
    // Send GitHub updates to all subscribers
    sendGitHubUpdatesToSubscribers();
    
    // Log success
    $logMessage = "[$timestamp] CRON executed successfully - GitHub updates sent\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    
    echo "CRON job executed successfully at $timestamp\n";
    
} catch (Exception $e) {
    // Log error
    $errorMessage = "[$timestamp] CRON error: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND | LOCK_EX);
    
    echo "CRON job failed at $timestamp: " . $e->getMessage() . "\n";
}

?>