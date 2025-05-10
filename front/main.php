<?php
// This MUST be at the VERY TOP of your file, before ANY HTML
session_start();

// Check if visitor is logged in
$isVisitor = isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur';
$visitorName = $_SESSION['prenom'] ?? 'Visitor';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $isVisitor = isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur';
    if (!$isVisitor) {
        header("Location: login.php");
        exit();
    }
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <style>
        /* Add this style for the visitor name display */
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



.logout-button i {
    font-size: 20px;
}
    </style>
</head>
<body>
    <header>
        <div class="first-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="logo"><a href="main.php">Time Travel</a></div>
          
            <div class="buttons">
                <button><a href="boutique.php" class="header-button">Online Boutique</a></button>
                <button><a href="tickets.php" class="header-button">Tickets</a></button>
                <?php if($isVisitor): ?>
                <div class="visitor-container">
                    <div class="visitor-name">
                        <i class='bx bx-user'></i>
                        <span><?php echo htmlspecialchars($visitorName); ?></span>
                    </div>
                    <a href="logout.php" class="logout-button">
                        <i class='bx bx-log-out'></i>
                    </a>
                </div>
                <?php else: ?>
                    <button><a href="login.php" class="header-button">Login</a></button>
                <?php endif; ?>
            </div>
        </div>

        <div class="second-header"> 
            <nav>
                <ul>
                    <li><a href="main.php">Home</a></li>
                    <li><a href="events.php">Exibitions & Events</a></li>
                    <li><a href="#visit">Visit</a></li>
                    <li><a href="collections.php">Collections</a></li>
                    <li><a href="#Contacts">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="hero-container">
            <img src="pics/claudio-testa-iqeG5xA96M4-unsplash (1).jpg" alt="Museum Exhibit" class="hero">
            <div class="hero-overlay">
                <h1 class="hero-title">Discover Time Travel</h1>
                <p class="hero-subtitle">Explore masterpieces from around the world.</p>
                <button class="hero-button"><a href="#visit">Plan Your Visit</a></button>
                <button class="hero-button"><a href="tickets.html">Book Your Ticket</a></button>
            </div>
        </section>
        
        <section class="exhibition" id="exhibition">
    <div class="section-header">
        <h2>Current Exhibitions & Events</h2>
        <p>Explore our featured exhibitions from different eras</p>
    </div>
    
    <div class="exhibition-grid">
        <?php
        require 'config.php';

        // Fetch only 4 current or upcoming exhibitions
        $currentDate = date('Y-m-d');
        $exhibitions = [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM evenement 
                                WHERE date_fin >= :currentDate
                                ORDER BY date_debut ASC 
                                LIMIT 4");
            $stmt->execute(['currentDate' => $currentDate]);
            $exhibitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching exhibitions: " . $e->getMessage());
        }
        ?>

        <?php if (empty($exhibitions)): ?>
            <p class="no-exhibition">No current exhibitions</p>
        <?php else: ?>
            <?php foreach ($exhibitions as $exhibition): ?>
                <div class="exhibition-card">
                    <div class="exhibition-image-container">
                        <?php if (!empty($exhibition['image_evenement'])): ?>
                            <img src="<?php echo htmlspecialchars($exhibition['image_evenement']); ?>" alt="<?php echo htmlspecialchars($exhibition['titre']); ?>" class="exhibition-image">
                        <?php else: ?>
                            <img src="pics/default-exhibition.jpg" alt="Exhibition Image" class="exhibition-image">
                        <?php endif; ?>
                        <div class="exhibition-dates">
                            <?php echo date('M j', strtotime($exhibition['date_debut'])); ?> - <?php echo date('M j, Y', strtotime($exhibition['date_fin'])); ?>
                        </div>
                    </div>
                    <div class="exhibition-content">
                        <h3 class="exhibition-title"><?php echo htmlspecialchars($exhibition['titre']); ?></h3>
                        <p class="exhibition-location"><?php echo htmlspecialchars($exhibition['lieu']); ?></p>
                        <p class="exhibition-description">
                            <?php echo htmlspecialchars(substr($exhibition['description'], 0, 150)); ?>
                            <?php if (strlen($exhibition['description']) > 150) echo '...'; ?>
                        </p>
                        <a href="event-details.php?id=<?php echo $exhibition['id']; ?>" class="exhibition-link">View Details →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

     <div class="view-all">
                <a href="events.php" class="view-all-button">View All Events</a>
            </div>
        </section>
</section>

<style>
    .exhibition {
        padding: 4rem 1rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .no-exhibition {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        font-size: 1.2rem;
        color: #666;
    }

    .exhibition-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        margin: 0 auto;
    }

    .exhibition-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .exhibition-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .exhibition-image-container {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .exhibition-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .exhibition-card:hover .exhibition-image {
        transform: scale(1.05);
    }

    .exhibition-dates {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .exhibition-content {
        padding: 1.5rem;
    }

    .exhibition-title {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: #000;
    }

    .exhibition-location {
        color: #666;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .exhibition-description {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .exhibition-link {
        color: #000;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        position: relative;
        padding-right: 20px;
        font-size: 0.95rem;
    }

    .exhibition-link::after {
        content: '→';
        position: absolute;
        right: 0;
        transition: right 0.3s ease;
    }

    .exhibition-link:hover::after {
        right: -5px;
    }

    

   
    

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-header h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #000;
    }

    .section-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .exhibition-grid {
            grid-template-columns: 1fr;
        }
        
        .section-header h2 {
            font-size: 1.8rem;
        }
    }
</style>

        
        <section class="visit-info" id="visit">
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

        <section class="collections" id="collections">
    <div class="section-header">
        <h2>Our Collections</h2>
        <p>Journey through humanity's greatest achievements across time</p>
    </div>
    
    <div class="collection-grid">
        <?php
        require 'config.php';

        // Fetch only 4 active artworks
        $artworks = [];
        try {
            $stmt = $pdo->query("SELECT * FROM oeuvres ORDER BY date_creation DESC LIMIT 4");
            $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching artworks: " . $e->getMessage());
        }
        ?>

        <?php if (empty($artworks)): ?>
            <p class="no-artworks">No artworks available</p>
        <?php else: ?>
            <?php foreach ($artworks as $artwork): ?>
                <div class="collection-card">
                    <div class="collection-image">
                        <?php if (!empty($artwork['image_oeuvre'])): ?>
                            <img src="<?php echo htmlspecialchars($artwork['image_oeuvre']); ?>" alt="<?php echo htmlspecialchars($artwork['titre']); ?>">
                        <?php else: ?>
                            <img src="pics/default-artwork.jpg" alt="Artwork Image">
                        <?php endif; ?>
                        <div class="time-period">
                            <?php echo !empty($artwork['date_creation']) ? htmlspecialchars($artwork['date_creation']) : 'Undated'; ?>
                        </div>
                    </div>
                    <div class="collection-content">
                        <h3><?php echo htmlspecialchars($artwork['titre']); ?></h3>
                        <p>
                            <?php echo htmlspecialchars($artwork['artiste']); ?>
                            <?php if (!empty($artwork['type_oeuvre'])): ?>
                                <br><?php echo htmlspecialchars($artwork['type_oeuvre']); ?>
                            <?php endif; ?>
                        </p>
                        <a href="artwork-details.php?id=<?php echo $artwork['id']; ?>" class="collection-link">Explore →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="view-all">
        <a href="collections.php" class="view-all-button">View All Collections</a>
    </div>
    <style>
        .no-artworks {
    grid-column: 1 / -1;
    text-align: center;
    padding: 2rem;
    font-size: 1.2rem;
    color: #666;
}

.collection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.collection-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.collection-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.collection-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.collection-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.collection-card:hover .collection-image img {
    transform: scale(1.05);
}

.time-period {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.collection-content {
    padding: 1.5rem;
}

.collection-content h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #000;
}

.collection-content p {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.collection-link {
    color: #000;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    position: relative;
    padding-right: 20px;
}

.collection-link::after {
    content: '→';
    position: absolute;
    right: 0;
    transition: right 0.3s ease;
}

.collection-link:hover::after {
    right: -5px;
}

    </style>
</section>


<!-- Review Section -->
<?php include 'review-section.php'; ?>


    <!--contct-->
        <section class="contact" id="Contacts">
            <div class="section-header">
                <h2>Contact Us</h2>
                <p>Have questions? Reach out to our team</p>
            </div>
            
            <div class="contact-container">
                <form class="contact-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" placeholder="Your name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" placeholder="Your email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <div class="input-with-icon">
                            <i class="fas fa-comment"></i>
                            <textarea id="message" placeholder="Your message"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="contact-button">Send Message</button>
                </form>
                
                <div class="contact-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Location</h3>
                            <p>88 Time Travel Avenue, Chronopolis</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>+1 555-TIME-TRV</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>info@timetravelmuseum.org</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!--footer-->

<section class="footer">
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
</section>
   </section>
<!--copyright-->
<div class="copyright">
    <p>&#169; Time Travel All Rights Reserved 2023</p>
</div>
</body>
</html>