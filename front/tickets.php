<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum - Tickets</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Tickets Hero Section */
        .tickets-hero {
            position: relative;
            height: 500px;
            overflow: hidden;
            margin-top: 0;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('pics/ticket.avif');
            background-size: cover;
            background-position: center;
        }

        .tickets-hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            width: 100%;
            padding: 0 2rem;
            margin-top: 3%;
        }

        .tickets-hero {
    position: relative;
    height: 500px;
    overflow: hidden;
    margin-top: 0;
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
    url(pics/ticket.avif);
}


.tickets-hero-content {
    
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    width: 100%;
    padding: 0 2rem;
    margin-top: 3%;
}

.tickets-hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}
.visitor-name {
            display: flex;
            align-items: center;
            gap: 8px;
            background: black;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .visitor-name i {
            font-size: 18px;
        }
        .visitor-container {
    display: flex;
    align-items: center;
    gap: 10px;
}



.visitor-name i {
    font-size: 18px;
}

.logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: black;
    border-radius: 50%;
    color: #333;
    transition: all 0.3s ease;
    background: #ddd;
    color: #000;
}

.tickets-hero-content p {
    font-size: 1.2rem;
    max-width: 700px;
    margin: 0 auto 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    margin-bottom: 90px;
}

/* Ticket Types Section */
.ticket-types {
    padding: 4rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.section-title {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 1rem;
    color: #000;
}

.section-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.2rem;
}

.ticket-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.ticket-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    display: flex; /* Add flexbox */
    flex-direction: column; /* Stack children vertically */
    height: 100%; /* Make all cards equal height */
}

.ticket-card:hover {
    transform: translateY(-10px);
}

.ticket-header {
    background: #000;
    color: white;
    padding: 1.5rem;
    text-align: center;
}

.ticket-header h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.ticket-header .price {
    font-size: 2rem;
    font-weight: 700;
    color: #d4af37;
}

.ticket-header .price span {
    font-size: 1rem;
    font-weight: 400;
    color: white;
}

.ticket-body {
    padding: 1.5rem;
    flex-grow: 1; /* Push button to bottom */
    display: flex; /* Nested flexbox */
    flex-direction: column;
}

.ticket-features {
    list-style: none;
    margin-bottom: 2rem;
    flex-grow: 1; /* Take remaining space */
}


.ticket-features li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.ticket-features li:last-child {
    border-bottom: none;
}

.ticket-features i {
    color: #d4af37;
    margin-right: 0.5rem;
}

.ticket-button {
    display: block;
    width: 100%;
    padding: 1rem;
    background: #000;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    text-align: center;
    text-decoration: none;
    margin-top: auto; /* Push button to bottom */
}

.ticket-button:hover {
    background: #333;
}

/* Membership Section */
.membership-section {
    background: #f8f8f8;
    padding: 4rem 2rem;
    text-align: center;
}

.membership-content {
    max-width: 800px;
    margin: 0 auto;
}

.membership-content h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.membership-content p {
    color: #666;
    margin-bottom: 1.5rem;
}

.membership-button {
    display: inline-block;
    padding: 1rem 2rem;
    background: #d4af37;
    color: #000;
    font-weight: 600;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s;
}

.membership-button:hover {
    background: #000;
    color: white;
}

/* Visit Info Section */
.visit-info {
    padding: 4rem 2rem;
    background: #000;
    color: white;
}

.info-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.info-card {
    text-align: center;
    padding: 2rem;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

.info-card h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.info-card p {
    margin-bottom: 0.5rem;
    color: #ccc;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .first-header {
        flex-direction: column;
        height: auto;
        padding: 1rem;
    }
    
    .second-header {
        top: 120px; /* Adjusted for mobile */
    }
    
    main {
        margin-top: 170px; /* Adjusted for mobile */
    }
    
    .tickets-hero {
        height: 400px;
    }
    
    .tickets-hero-content h1 {
        font-size: 2.5rem;
    }
    
    .tickets-hero-content p {
        font-size: 1rem;
    }
    
    .ticket-types {
        padding: 2rem 1rem;
    }
}

@media (max-width: 480px) {
    .ticket-grid {
        grid-template-columns: 1fr;
    }
    
    .tickets-hero-content h1 {
        font-size: 2rem;
    }
}
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <main>
        <section class="tickets-hero">
            <div class="tickets-hero-content">
                <h1>Plan Your Visit</h1>
                <p>Journey through time with our immersive exhibitions. Book your tickets today and experience history like never before.</p>
            </div>
        </section>

        <section class="ticket-types">
            <h2 class="section-title">Ticket Options</h2>
            <p class="section-subtitle">Choose the experience that's right for you</p>
            
            <div class="ticket-grid">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM billets WHERE id_evenement IS NULL");
                    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($tickets)) {
                        echo '<p class="no-tickets">No tickets currently available</p>';
                    } else {
                        foreach ($tickets as $ticket) {
                            echo '<div class="ticket-card">';
                            echo '<div class="ticket-header">';
                            echo '<h3>'.htmlspecialchars($ticket['type_billet']).'</h3>';
                            echo '<div class="price">$'.number_format($ticket['tarif'], 2).'</div>';
                            echo '</div>';
                            echo '<div class="ticket-body">';
                            echo '<ul class="ticket-features">';
                            
                            // Dynamic features based on ticket type
                            echo '<li><i class="fas fa-check"></i> Access to all permanent exhibitions</li>';
                            if (strpos($ticket['type_billet'], 'VIP') !== false) {
                                echo '<li><i class="fas fa-check"></i> Skip-the-line priority access</li>';
                                echo '<li><i class="fas fa-check"></i> Private guided tour</li>';
                            }
                            if (strpos($ticket['type_billet'], 'Family') !== false) {
                                echo '<li><i class="fas fa-check"></i> Admission for 2 adults + 2 children</li>';
                                echo '<li><i class="fas fa-check"></i> Special family activities</li>';
                            }
                            if ($ticket['reduction'] > 0) {
                                echo '<li><i class="fas fa-check"></i> '.number_format($ticket['reduction'], 0).'% discount</li>';
                            }
                            
                            echo '</ul>';
                            
                            if (isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur') {
                                echo '<a href="book-ticket.php?id='.$ticket['id'].'" class="ticket-button">Book Now</a>';
                            } else {
                                echo '<a href="login.php?redirect='.urlencode($_SERVER['REQUEST_URI']).'" class="ticket-button">Login to Book</a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">Error loading tickets. Please try again later.</div>';
                    error_log("Ticket error: ".$e->getMessage());
                }
                ?>
            </div>
        </section>

        <section class="membership-section">
            <div class="membership-content">
                <h2>Become a Time Travel Member</h2>
                <p>Enjoy unlimited access to the museum, exclusive events, and special discounts throughout the year with our membership program.</p>
                <a href="membership.html" class="membership-button">Explore Membership Options</a>
            </div>
        </section>

        <section class="visit-info">
            <div class="info-container">
                <div class="info-card">
                    <h3>Opening Hours</h3>
                    <p>Monday-Friday: 9am-6pm</p>
                    <p>Weekends: 10am-8pm</p>
                </div>
                <div class="info-card">
                    <h3>Location</h3>
                    <p>88 Time Travel Avenue</p>
                    <p>Chronopolis, TT 2025</p>
                </div>
                <div class="info-card">
                    <h3>Contact</h3>
                    <p>info@timetravelmuseum.org</p>
                    <p>+1 555-TIME-TRV</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="end">
            <div class="company-info">
                <h2>Time Travel</h2>
            </div>
            <div class="social">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-tiktok'></i></a>
            </div>
        </div>
        <div class="ends">
            <div class="support">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">Product</a></li>
                    <li><a href="#">Help & Support</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">Terms Of Use</a></li>
                </ul>
            </div>
            <div class="guides">
                <h3>View Guides</h3>
                <ul>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog Posts</a></li>
                    <li><a href="#">Our Branches</a></li>
                </ul>
            </div>
            <div class="contacts">
                <h3>Contacts</h3>
                <ul class="contact-list">
                    <li><i class='bx bxs-map'></i>25 Oujda City, 60000</li>
                    <li><i class='bx bxs-phone'></i>+212 66648 74442</li>
                    <li><i class='bx bxs-mail-send'></i>ZertCrew@shop.com</li>
                </ul>
            </div>
        </div>
    </footer>

    <div class="copyright">
        <p>&#169; Time Travel All Rights Reserved 2023</p>
    </div>
</body>
</html>