<?php
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['text']; // This could be username or email
    $password = $_POST['password'];

    try {
        // Check if the identifier exists in the database
        $stmt = $pdo->prepare("SELECT * FROM employe WHERE username = ?");
        $stmt->execute([$identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch (strtolower($user['role'])) {
                case 'admin':
                    header('Location: admin-dashboard.php');
                    break;
                case 'oeuvres':
                    header('Location: collection-list.php');
                    break;
                case 'visiteurs':
                    header('Location: Evisiteurs-dashboard.php');
                    break;
                case 'evenements':
                    header('Location: events-list.php');
                    break;
                default:
                    // Default redirect if role doesn't match
                    header('Location: login.php?error=no_dashboard');
            }
            exit();
        } else {
            // Login failed
            header('Location: login.php?error=invalid_credentials');
            exit();
        }
    } catch (PDOException $e) {
        // Database error
        error_log("Database error: " . $e->getMessage());
        header('Location: login.php?error=database_error');
        exit();
    }
} else {
    // Not a POST request
    header('Location: login.php');
    exit();
}
