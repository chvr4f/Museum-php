<?php
require 'config.php';

// Initialize variables
$errors = [];
$formData = [];

// Process form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $phone = trim($_POST['phone'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $terms = isset($_POST['terms']) ? true : false;

    // Store form data for repopulation
    $formData = [
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'age' => $age,
        'terms' => $terms
    ];

    // Split full name into first and last name
    $nameParts = explode(' ', $fullname, 2);
    $prenom = $nameParts[0];
    $nom = count($nameParts) > 1 ? $nameParts[1] : '';

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Validate phone (optional field)
    if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]{10,20}$/', $phone)) {
        $errors[] = "Invalid phone number format";
    }

    // Validate age
    if (empty($age)) {
        $errors[] = "Age is required";
    } elseif ($age < 1 || $age > 120) {
        $errors[] = "Age must be between 1 and 120";
    }

    // Validate terms
    if (!$terms) {
        $errors[] = "You must agree to the terms and conditions";
    }

    // Check if email already exists
    try {
        $stmt = $pdo->prepare("SELECT id FROM visiteur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered";
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Modified query - removed type_visiteur
            $stmt = $pdo->prepare("INSERT INTO visiteur 
                (email, password, nom, prenom, tel, age) 
                VALUES (?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $email, 
                $hashed_password, 
                $nom, 
                $prenom,
                $phone,
                $age
            ]);
            
            // Clear form data
            $formData = [];
            
            // Success - redirect to login with success message
            session_start();
            $_SESSION['registration_success'] = true;
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // If we got here, there were errors
    session_start();
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_data'] = $formData;
    header('Location: register.php');
    exit();
}

// If someone accesses this page directly
header('Location: register.php');
exit();