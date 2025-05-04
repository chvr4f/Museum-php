<?php
require 'config.php';

// Fetch all active artworks
$artworks = [];
try {
    $stmt = $pdo->query("SELECT * FROM oeuvres ORDER BY date_creation DESC");
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching artworks: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Collections - Time Travel Museum</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <!-- Include your header content from main.php -->
        <?php include 'header.php'; ?>
    </header>

    <main>
        <section class="collections" id="collections">
            <div class="section-header">
                <h2>All Artwork Collections</h2>
                <p>Explore our complete collection of artworks from all time periods</p>
            </div>
            
            <div class="collection-grid">
                <?php if (empty($artworks)): ?>
                    <p class="no-artworks">No artworks available at this time.</p>
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
                                <a href="artwork-details.php?id=<?php echo $artwork['id']; ?>" class="collection-link">View Details →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <!-- Include your footer content from main.php -->
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>

<style>
    /* Collections Page Specific Styles */
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