<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
    $comment = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);
    $visitor_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $item_type = filter_input(INPUT_POST, 'item_type', FILTER_SANITIZE_STRING);
    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $pdo->prepare("INSERT INTO avis (id_visiteur, notes, commentaire, date_avis, 
                              id_oeuvres, id_evenement, id_article) 
                              VALUES (?, ?, ?, NOW(), ?, ?, ?)");
        
        // Set the appropriate ID based on item type
        $oeuvre_id = $item_type === 'oeuvre' ? $item_id : null;
        $event_id = $item_type === 'event' ? $item_id : null;
        $article_id = $item_type === 'article' ? $item_id : null;
        
        $stmt->execute([
            $visitor_id,
            $rating,
            $comment,
            $oeuvre_id,
            $event_id,
            $article_id
        ]);
        
        $_SESSION['review_success'] = "Thank you for your review!";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $_SESSION['review_error'] = "Error submitting review: " . $e->getMessage();
    }
}

// Fetch reviews from database
$reviews = [];
try {
    $query = "SELECT a.*, v.nom, v.prenom 
              FROM avis a
              LEFT JOIN visiteur v ON a.id_visiteur = v.id
              ORDER BY a.date_avis DESC
              LIMIT 10";
    $stmt = $pdo->query($query);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching reviews: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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

        .submit-btn {
            background: black;
            color: white;
            border: none;
            padding: 0.8rem;
            font-size: 0.95rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: 500;
            margin-top: 0.5rem;
            width: 100%;
        }

        .submit-btn:hover {
            background: white;

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
            background: white;
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
                    <form id="reviewForm" class="review-form">
                        <div class="form-group">
                            <label>Your Rating:</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5" title="5 stars"><i class='bx bxs-star'></i></label>
                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="4 stars"><i class='bx bxs-star'></i></label>
                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="3 stars"><i class='bx bxs-star'></i></label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="2 stars"><i class='bx bxs-star'></i></label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="1 star"><i class='bx bxs-star'></i></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="review">Your Review:</label>
                            <textarea id="review" name="review" required placeholder="Tell us about your experience..." class="form-control"></textarea>
                        </div>

                        <button type="submit" class="view-all-button">Submit Review</button>
                    </form>
                </div>
            </div>

            <!-- Right Column - Scrollable Reviews -->
            <div class="reviews-column">
                <div class="review-cards">
                    <!-- Review 1 -->
                    <div class="review-card">
                        <div class="review-stars">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                        <p class="review-text">"Amazing service! Product quality exceeded my expectations. Fast shipping and eco-friendly packaging."</p>
                        <div class="reviewer-info">
                            <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="Sarah Johnson" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h4>Sarah Johnson</h4>
                                <p>Verified • May 2023</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="review-card">
                        <div class="review-stars">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star-half'></i>
                        </div>
                        <p class="review-text">"Great experience overall. Product works as described. Responsive customer service."</p>
                        <div class="reviewer-info">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael Chen" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h4>Michael Chen</h4>
                                <p>Verified • Apr 2023</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="review-card">
                        <div class="review-stars">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                        <p class="review-text">"Changed my routine completely. Outstanding quality worth every penny. Highly recommend!"</p>
                        <div class="reviewer-info">
                            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Emily Rodriguez" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h4>Emily Rodriguez</h4>
                                <p>Verified • Mar 2023</p>
                            </div>
                        </div>
                    </div>

                    <!-- Review 4 -->
                    <div class="review-card">
                        <div class="review-stars">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                        <p class="review-text">"Third purchase and still impressed. Remarkable attention to detail. Committed to sustainability."</p>
                        <div class="reviewer-info">
                            <img src="https://randomuser.me/api/portraits/men/55.jpg" alt="David Wilson" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h4>David Wilson</h4>
                                <p>Verified • Jun 2023</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>