<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'config.php';

// Check if user is logged in as visitor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'visiteur') {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

// Check if ticket ID is provided
if (!isset($_GET['id'])) {
    header('Location: tickets.php');
    exit();
}

$ticketId = (int)$_GET['id'];
$visitorId = $_SESSION['user_id'];

try {
    // Verify ticket exists
    $stmt = $pdo->prepare("SELECT * FROM billets WHERE id = ? AND id_evenement IS NULL");
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        $_SESSION['ticket_error'] = "Invalid ticket selection";
        header('Location: tickets.php');
        exit();
    }

    // Process booking if form submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quantity = (int)$_POST['quantity'];
        $visitDate = $_POST['visit_date'];
        
        // Validate inputs
        if ($quantity < 1 || $quantity > 10) {
            $error = "Quantity must be between 1 and 10";
        } elseif (strtotime($visitDate) < strtotime('today')) {
            $error = "Visit date cannot be in the past";
        } else {
            // Calculate total price
            $totalPrice = $ticket['tarif'] * $quantity;
            
            // Insert booking
            $stmt = $pdo->prepare("INSERT INTO reservations (id_billet, id_visiteur, quantite, date_visite, prix_total) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ticketId, $visitorId, $quantity, $visitDate, $totalPrice]);
            
            $_SESSION['booking_success'] = "Your ticket has been booked successfully!";
            header('Location: tickets.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Booking error: ".$e->getMessage());
    $error = "An error occurred. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket - Time Travel Museum</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function updatePrice() {
            const quantity = document.getElementById('quantity').value;
            const unitPrice = <?php echo $ticket['tarif']; ?>;
            const totalPrice = (unitPrice * quantity).toFixed(2);
            document.getElementById('total-price').textContent = '$' + totalPrice;
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>

    <main class="booking-container">
        <h1>Book Your Ticket</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="booking-details">
            <h2><?php echo htmlspecialchars($ticket['type_billet']); ?></h2>
            <p class="unit-price">Unit Price: $<?php echo number_format($ticket['tarif'], 2); ?></p>
            <p class="price">Total Price: <span id="total-price">$<?php echo number_format($ticket['tarif'], 2); ?></span></p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="quantity">Quantity (max 10):</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required 
                           onchange="updatePrice()" oninput="updatePrice()">
                </div>
                
                <div class="form-group">
                    <label for="visit_date">Visit Date:</label>
                    <input type="date" id="visit_date" name="visit_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <button type="submit" class="submit-button">Confirm Booking</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <style>
/* Main Booking Container */
.booking-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.booking-container h1 {
    font-size: 2.5rem;
    color: #000;
    text-align: center;
    margin-bottom: 2rem;
}

/* Alert Messages */
.alert {
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 4px;
    font-weight: 500;
}

.alert-error {
    background-color: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
}

.alert-success {
    background-color: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

/* Booking Details Section */
.booking-details {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 0 auto;
}

.booking-details h2 {
    font-size: 1.8rem;
    color: #000;
    margin-bottom: 0.5rem;
    text-align: center;
}

.unit-price {
    font-size: 1.2rem;
    color: #666;
    text-align: center;
    margin-bottom: 0.5rem;
}

.price {
    font-size: 1.8rem;
    color: #d4af37;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: 'Montserrat', sans-serif;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #000;
}

/* Submit Button */
.submit-button {
    width: 100%;
    padding: 1rem;
    background: #000;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 1rem;
    margin-top: 1rem;
}

.submit-button:hover {
    background: #333;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .booking-container {
        padding: 0 1rem;
    }
    
    .booking-container h1 {
        font-size: 2rem;
    }
    
    .booking-details {
        padding: 1.5rem;
    }
    
    .booking-details h2 {
        font-size: 1.5rem;
    }
    
    .price {
        font-size: 1.6rem;
    }
}

@media (max-width: 480px) {
    .booking-container h1 {
        font-size: 1.8rem;
    }
    
    .form-group input {
        padding: 0.7rem 0.9rem;
    }
    
    .submit-button {
        padding: 0.9rem;
    }
}
</style>
</body>
</html>