<?php
session_start();
require_once 'config.php';

// Check if user is logged in
$isVisitor = isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!$isVisitor) {
        header("Location: login.php");
        exit();
    }

    // Get inputs
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_FLOAT, [
        'options' => ['min_range' => 0, 'max_range' => 5]
    ]);
    $comment = trim($_POST['review'] ?? '');
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    // Get visitor ID from session
    $id_visiteur = $_SESSION['id_visiteur'] ?? null;

    // Validation
    if (!$id_visiteur || !is_numeric($id_visiteur)) {
        $_SESSION['error'] = "User not logged in or invalid session.";
    } elseif ($rating === false || $rating < 0 || $rating > 5) {
        $_SESSION['error'] = "Please provide a valid rating between 0 and 5.";
    } elseif (empty($comment)) {
        $_SESSION['error'] = "Review cannot be empty.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO avis (id_visiteur, notes, commentaire) VALUES (?, ?, ?)");
            $stmt->execute([$id_visiteur, $rating, $comment]);
            $_SESSION['message'] = "Thank you for your review!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
    }

    // Redirect to prevent resubmission
    header("Location: main.php#reviews");
    exit();
}