<?php
// Common utility functions for the application

/**
 * Initialize user session if not already started
 */
function userSessionInit() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} 