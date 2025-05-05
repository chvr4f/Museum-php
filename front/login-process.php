<?php
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['text']; // This could be username or email
    $password = $_POST['password'];

    try {
        // First check the employe table
        $stmt = $pdo->prepare("SELECT * FROM employe WHERE username = ?");
        $stmt->execute([$identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Employee login successful
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
                    header('Location: visitors-list.php');
                    break;
                case 'evenements':
                    header('Location: events-list.php');
                    break;
                default:
                    header('Location: login.php?error=no_dashboard');
            }
            exit();
        } else {
            // If not found in employe table, check visiteur table
            $stmt = $pdo->prepare("SELECT * FROM visiteur WHERE email = ?");
            $stmt->execute([$identifier]);
            $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($visitor && password_verify($password, $visitor['password'])) {
                // Visitor login successful
                $_SESSION['user_id'] = $visitor['id'];
                $_SESSION['email'] = $visitor['email'];
                $_SESSION['role'] = 'visiteur';
                $_SESSION['nom'] = $visitor['nom'];
                $_SESSION['prenom'] = $visitor['prenom'];

                header('Location: main.php');
                exit();
            } else {
                // Login failed for both tables
                header('Location: login.php?error=invalid_credentials');
                exit();
            }
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