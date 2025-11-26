<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect back to homepage
header("Location: index.php");
exit;