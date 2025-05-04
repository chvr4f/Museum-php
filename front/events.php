<?php
require 'config.php';

// Fetch all active events (current and future)
$currentDate = date('Y-m-d');
$events = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM evenement 
                          WHERE date_fin >= :currentDate
                          ORDER BY date_debut DESC");
    $stmt->execute(['currentDate' => $currentDate]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching events: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - Time Travel Museum</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Events Page Specific Styles - Matching Collections */
        .exhibitions {
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

    .view-all {
        text-align: center;
        margin-top: 3rem;
    }

    .view-all-button {
        display: inline-block;
        padding: 0.8rem 2rem;
        background-color: #000;
        color: white;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .view-all-button:hover {
        background-color: #333;
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
</head>
<body>
    <header>
        <?php include 'header.php'; ?>
    </header>

    <main>
        <section class="exhibition" id="exhibition">
            <div class="section-header">
                <h2>All Events & Exhibitions</h2>
                <p>Explore our complete collection of current and upcoming events</p>
            </div>
            
            <div class="exhibition-grid">
                <?php if (empty($events)): ?>
                    <p class="no-exhibition">No events available at this time.</p>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <div class="exhibition-card">
                            <div class="exhibition-image-container">
                                <?php if (!empty($event['image_evenement'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image_evenement']); ?>" alt="<?php echo htmlspecialchars($event['titre']); ?>" class="exhibition-image">
                                <?php else: ?>
                                    <img src="pics/default-event.jpg" alt="Event Image" class="exhibition-image">
                                <?php endif; ?>
                                <div class="exhibition-dates">
                                    <?php echo date('M j', strtotime($event['date_debut'])); ?> - <?php echo date('M j, Y', strtotime($event['date_fin'])); ?>
                                </div>
                            </div>
                            <div class="exhibition-content">
                                <h3 class="exhibition-title"><?php echo htmlspecialchars($event['titre']); ?></h3>
                                <p class="exhibition-location"><?php echo htmlspecialchars($event['lieu']); ?></p>
                                <p class="exhibition-description">
                                    <?php echo htmlspecialchars(substr($event['description'], 0, 150)); ?>
                                    <?php if (strlen($event['description']) > 150) echo '...'; ?>
                                </p>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="exhibition-link">View Details →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>