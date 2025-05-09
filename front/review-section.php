<?php require_once 'config.php'; ?>

<?php
try {
    $stmt = $pdo->query("SELECT a.*, v.prenom, v.nom FROM avis a JOIN visiteur v ON a.id_visiteur = v.id ORDER BY date_avis DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reviews = [];
}


// Fetch all reviews
try {
    $stmt = $pdo->prepare("
        SELECT a.*, v.prenom, v.nom 
        FROM avis a 
        JOIN visiteur v ON a.id_visiteur = v.id 
        ORDER BY date_avis DESC
    ");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link href='https://unpkg.com/boxicons @2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Modern Review Section */
        .reviews-container {
            max-width: 2000px;
            margin: 1rem auto;
            padding: 0 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .section-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .section-title h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: #666;
            font-size: 1rem;
        }

        /* Two-column layout */
        .reviews-layout {
            display: flex;
            gap: 1.5rem;
        }

        .review-form-column {
            flex: 1;
            min-width: 350px;
        }

        .reviews-column {
            flex: 1;
            min-width: 350px;
        }

        /* Review Form */
        .add-review {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }

        .add-review h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 0.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 0.5rem 0 1rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 1.8rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffc107;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }


        /* Scrollable Reviews */
        .review-cards {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.8rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Custom scrollbar */
        .review-cards::-webkit-scrollbar {
            width: 5px;
        }

        .review-cards::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .review-cards::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .review-cards::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .review-card {
            background:#e1ebe2 ;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            padding: 1.2rem;
            transition: transform 0.2s ease;
        }

        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .review-stars {
            color: #ffc107;
            font-size: 1.1rem;
            margin-bottom: 0.6rem;
        }

        .review-text {
            color: #444;
            line-height: 1.5;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
        }

        .reviewer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.8rem;
            border: 1px solid #f0f0f0;
        }

        .reviewer-details h4 {
            margin: 0;
            color: #333;
            font-size: 0.95rem;
        }

        .reviewer-details p {
            margin: 0.1rem 0 0;
            color: #777;
            font-size: 0.8rem;
        }
        .reviewer-details {
    margin-top: 0.5rem;
}

.reviewer-details h4 {
    margin: 0;
    font-size: 0.95rem;
    color: #333;
}

.reviewer-details p {
    margin: 0.2rem 0 0;
    font-size: 0.8rem;
    color: #777;
}

        /* Responsive adjustments */
        @media (max-width: 800px) {
            .reviews-layout {
                flex-direction: column;
            }

            .review-form-column,
            .reviews-column {
                width: 100%;
            }

            .review-cards {
                max-height: none;
                overflow-y: visible;
            }

            .add-review {
                position: static;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .section-title h2 {
                font-size: 1.5rem;
            }

            .add-review {
                padding: 1.2rem;
            }

            .add-review h3 {
                font-size: 1.1rem;
            }

            .star-rating label {
                font-size: 1.5rem;
            }

            textarea.form-control {
                min-height: 80px;
            }
        }
    </style>
</head>
<body>

<div class="reviews-container">
    <div class="section-title">
        <h2>Customer Reviews</h2>
        <p>See what our customers say about their experience</p>
    </div>

    <div class="reviews-layout">
        <!-- Left Column - Review Form -->
        <div class="review-form-column">
            <div class="add-review">
                <h3>Share Your Experience</h3>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php elseif (isset($_SESSION['error'])): ?>
                    <div class="alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <form id="reviewForm" class="review-form" method="post" action="submit-review.php">
                    <div class="form-group">
                        <label>Your Rating:</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5"><i class='bx bxs-star'></i></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4"><i class='bx bxs-star'></i></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3"><i class='bx bxs-star'></i></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2"><i class='bx bxs-star'></i></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1"><i class='bx bxs-star'></i></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review">Your Review:</label>
                        <textarea id="review" name="review" required placeholder="Tell us about your experience..." class="form-control"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="submit-btn">Submit Review</button>
                </form>
            </div>
        </div>

        <!-- Right Column - Scrollable Reviews -->
        <div class="reviews-column">
            <div class="review-cards">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-stars">
                                <?php
                                    $fullStars = floor($review['notes']);
                                    $halfStar = ($review['notes'] - $fullStars) >= 0.5;
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<i class="bx bxs-star"></i>';
                                    }
                                    if ($halfStar) {
                                        echo '<i class="bx bxs-star-half"></i>';
                                    }
                                ?>
                            </div>
                            <p class="review-text">"<?php echo nl2br(htmlspecialchars($review['commentaire'])); ?>"</p>
                            <div class="reviewer-details">
                                <h4><?php echo htmlspecialchars($review['prenom'] . ' ' . $review['nom']); ?></h4>
                                <p>Verified • <?php echo date('M Y', strtotime($review['date_avis'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>