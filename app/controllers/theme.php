<?php
session_start();
include "../database/db.php";

// Set the theme in the session
if (isset($_GET['theme'])) {
    $theme = $_GET['theme'];
    if ($theme === 'dark' || $theme === 'light') {
        $_SESSION['theme'] = $theme;

        // Optionally update the database if the user is logged in
        if (isset($_SESSION['id'])) {
            global $pdo;
            // First check if the column exists in the users table
            $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'theme'");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $update = $pdo->prepare("UPDATE users SET theme = ? WHERE id = ?");
                $update->execute([$theme, $_SESSION['id']]);
            }
        }

        echo json_encode(['status' => 'success', 'theme' => $theme]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid theme']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No theme provided']);
}
?>