<?php
session_start();
require 'config.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'evenements') {
    header('Location: login.html');
    exit();
}

// Initialize variables
$success = $error = '';
$event = [
    'titre' => '',
    'description' => '',
    'date_debut' => '',
    'date_fin' => '',
    'lieu' => '',
    'capacite' => '',
    'image_evenement' => ''
];
$edit_mode = false;
$current_image = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image_evenement']) && $_FILES['image_evenement']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/evenements/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image_evenement']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image_evenement']['tmp_name'], $destination)) {
                $imagePath = $destination;
                // Delete old image if exists
                if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                    unlink($_POST['current_image']);
                }
            }
        } elseif (!empty($_POST['current_image'])) {
            $imagePath = $_POST['current_image'];
        }

        if (isset($_POST['cancel'])) {
            // Cancel event (you might want to implement soft delete instead)
            $stmt = $pdo->prepare("DELETE FROM evenement WHERE id = ? AND id_employe = ?");
            $stmt->execute([$_POST['event_id'], $_SESSION['user_id']]);
            
            // Delete the associated image file
            if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                unlink($_POST['current_image']);
            }
            
            $success = "Event cancelled successfully!";
        } elseif (isset($_POST['update'])) {
            // Update event
            $stmt = $pdo->prepare("UPDATE evenement SET 
                titre = ?, description = ?, date_debut = ?, date_fin = ?, 
                lieu = ?, capacite = ?, image_evenement = ?
                WHERE id = ? AND id_employe = ?");
            
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['lieu'],
                $_POST['capacite'],
                $imagePath,
                $_POST['event_id'],
                $_SESSION['user_id']
            ]);
            
            $success = "Event updated successfully!";
        } else {
            // Add new event
            $stmt = $pdo->prepare("INSERT INTO evenement 
                (titre, description, date_debut, date_fin, lieu, capacite, image_evenement, id_employe) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['lieu'],
                $_POST['capacite'],
                $imagePath,
                $_SESSION['user_id']
            ]);
            
            $success = "Event added successfully!";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM evenement WHERE id = ? AND id_employe = ?");
        $stmt->execute([$_GET['edit'], $_SESSION['user_id']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            $edit_mode = true;
            $current_image = $event['image_evenement'];
        } else {
            $error = "Event not found or you don't have permission to edit it";
        }
    } catch (PDOException $e) {
        $error = "Error fetching event: " . $e->getMessage();
    }
}

// Fetch all events added by this employee
$events = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM evenement WHERE id_employe = ? ORDER BY date_debut DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching events: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Dashboard - Time Travel Museum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reuse the same styles as Eoeuvres-dashboard.php with minor adjustments */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #222;
            color: white;
            padding: 20px 0;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #444;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 10px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: #333;
            color: white;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 100px;
        }
        .btn {
            background: #222;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }
        .btn:hover {
            background: #333;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .event-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            background: white;
        }
        .event-image {
            height: 200px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }
        .event-details {
            padding: 15px;
        }
        .event-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .event-date {
            color: #666;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .event-location {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .edit-btn {
            background: #4CAF50;
            color: white;
        }
        .cancel-btn {
            background: #f44336;
            color: white;
        }
        .current-image {
            margin-top: 10px;
            max-width: 200px;
            max-height: 150px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Time Travel Museum</h2>
                <p>Events Management</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="Eevenements-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#card"><i class="fas fa-list"></i> All Events</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><?php echo $edit_mode ? 'Edit Event' : 'Add New Event'; ?></h1>
                <div>
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                    (<?php echo htmlspecialchars($_SESSION['role']); ?>)
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card" id="card">
                <form action="Eevenements-dashboard.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <input type="hidden" name="update" value="1">
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($event['image_evenement']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="titre">Title*</label>
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($event['titre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_debut">Start Date*</label>
                        <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($event['date_debut']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_fin">End Date*</label>
                        <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($event['date_fin']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lieu">Location*</label>
                        <input type="text" id="lieu" name="lieu" value="<?php echo htmlspecialchars($event['lieu']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacite">Capacity*</label>
                        <input type="number" id="capacite" name="capacite" value="<?php echo htmlspecialchars($event['capacite']); ?>" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="image_evenement">Event Image</label>
                        <input type="file" id="image_evenement" name="image_evenement" accept="image/*">
                        <?php if ($edit_mode && !empty($event['image_evenement'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($event['image_evenement']); ?>" class="current-image">
                                <p>Current image</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn">
                        <?php echo $edit_mode ? 'Update Event' : 'Add Event'; ?>
                    </button>
                    
                    <?php if ($edit_mode): ?>
                        <button type="submit" name="cancel" class="btn" style="background: #f44336; margin-left: 10px;" onclick="return confirm('Are you sure you want to cancel this event?')">Cancel Event</button>
                        <a href="Eevenements-dashboard.php" class="btn" style="background: #666; margin-left: 10px;">Back</a>
                    <?php endif; ?>
                </form>
            </div>

            <h2>Your Events</h2>
            <?php if (empty($events)): ?>
                <p>No events added yet.</p>
            <?php else: ?>
                <div class="event-grid">
                    <?php foreach ($events as $ev): ?>
                        <div class="event-card">
                            <div class="event-image" style="background-image: url('<?php echo htmlspecialchars($ev['image_evenement'] ?? 'https://via.placeholder.com/300x200?text=No+Image'); ?>')"></div>
                            <div class="event-details">
                                <div class="event-title"><?php echo htmlspecialchars($ev['titre']); ?></div>
                                <div class="event-date">
                                    <?php echo date('M j, Y', strtotime($ev['date_debut'])); ?> - 
                                    <?php echo date('M j, Y', strtotime($ev['date_fin'])); ?>
                                </div>
                                <div class="event-location"><?php echo htmlspecialchars($ev['lieu']); ?></div>
                                <div class="action-buttons">
                                    <a href="Eevenements-dashboard.php?edit=<?php echo $ev['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <form action="Eevenements-dashboard.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($ev['image_evenement']); ?>">
                                        <input type="hidden" name="cancel" value="1">
                                        <button type="submit" class="action-btn cancel-btn" onclick="return confirm('Are you sure you want to cancel this event?')">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>