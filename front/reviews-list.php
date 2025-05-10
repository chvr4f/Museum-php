<?php
session_start();
require 'config.php';

// Verify admin or visiteurs role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'visiteurs')) {
    header('Location: login.php');
    exit();
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    try {
        // First verify the review exists and belongs to user (or is admin)
        $checkStmt = $pdo->prepare("SELECT id_visiteur FROM avis WHERE id = ?");
        $checkStmt->execute([$_POST['review_id']]);
        $review = $checkStmt->fetch();

        if ($review && ($_SESSION['role'] === 'admin' || $review['id_visiteur'] == $_SESSION['user_id'])) {
            // Perform the actual deletion
            $deleteStmt = $pdo->prepare("DELETE FROM avis WHERE id = ?");
            $deleteStmt->execute([$_POST['review_id']]);
            
            if ($deleteStmt->rowCount() > 0) {
                $_SESSION['message'] = "Review deleted successfully";
            } else {
                $_SESSION['error'] = "Failed to delete review";
            }
        } else {
            $_SESSION['error'] = "You don't have permission to delete this review";
        }
        header('Location: reviews-list.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header('Location: reviews-list.php');
        exit();
    }
}

// Fetch reviews
try {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->prepare("
            SELECT a.*, v.prenom AS visitor_firstname, v.nom AS visitor_lastname
            FROM avis a
            JOIN visiteur v ON a.id_visiteur = v.id
            ORDER BY a.date_avis DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT a.*
            FROM avis a
            WHERE a.id_visiteur = ?
            ORDER BY a.date_avis DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching reviews: " . $e->getMessage();
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              600: '#2563eb',
              700: '#1d4ed8',
            },
            dark: {
              800: '#1e293b',
              900: '#0f172a',
            },
            admin: {
              bg: '#ffebee',
              text: '#c62828'
            },
            oeuvres: {
              bg: '#e3f2fd',
              text: '#1565c0'
            },
            evenements: {
              bg: '#e8f5e9',
              text: '#2e7d32'
            },
            visiteurs: {
              bg: '#fff3e0',
              text: '#ef6c00'
            },
            staff: {
              bg: '#f3e5f5',
              text: '#7b1fa2'
            }
          }
        }
      }
    }
  </script>
    <style>
      
        .review-card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .review-card:hover { box-shadow: 0 4px 6px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .star-rating { color: #fbbf24; }
        .scrollable-reviews { max-height: calc(100vh - 200px); overflow-y: auto; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 p-5">
            <div class="flex justify-between items-center mb-5">
                <h1 class="text-2xl font-bold">Manage Reviews</h1>
                <?php if ($_SESSION['role'] === 'visiteurs'): ?>
                    <a href="reviews-form.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Add New Review
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-6 scrollable-reviews">
                <?php if (empty($reviews)): ?>
                    <div class="bg-white rounded-lg p-8 text-center">
                        <p class="text-gray-500 text-lg">No reviews found</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 grid-cols-1 lg:grid-cols-2">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <h3 class="text-lg font-medium text-gray-900">
                                                Review by: <?php echo htmlspecialchars($review['visitor_firstname'] ?? '') . ' ' . htmlspecialchars($review['visitor_lastname'] ?? ''); ?>
                                            </h3>
                                        <?php else: ?>
                                            <h3 class="text-lg font-medium text-gray-900">Your Review</h3>
                                        <?php endif; ?>
                                    </div>
                                    <div class="star-rating flex items-center">
                                        <?php
                                            $rating = $review['notes'];
                                            $fullStars = floor($rating);
                                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                            
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="bx bxs-star text-xl"></i>';
                                            }
                                            if ($hasHalfStar) {
                                                echo '<i class="bx bxs-star-half text-xl"></i>';
                                            }
                                        ?>
                                        <span class="ml-1 text-gray-600"><?php echo number_format($rating, 1); ?></span>
                                    </div>
                                </div>
                                
                                <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($review['commentaire'])); ?></p>
                                
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span><?php echo date('M d, Y', strtotime($review['date_avis'])); ?></span>
                                    
                                    <form method="POST" action="reviews-list.php">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit" 
                                            class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
                                            onclick="return confirm('Are you sure you want to delete this review?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>