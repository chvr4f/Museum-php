<?php
session_start();
require 'config.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'oeuvres') {
    header('Location: login.html');
    exit();
}

// Initialize variables
$success = $error = '';
$artwork = [
    'titre' => '',
    'artiste' => '',
    'date_creation' => '',
    'type_oeuvre' => '',
    'materiaux' => '',
    'informations' => '',
    'image_oeuvre' => ''
];
$edit_mode = false;
$current_image = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image_oeuvre']) && $_FILES['image_oeuvre']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/oeuvres/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image_oeuvre']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image_oeuvre']['tmp_name'], $destination)) {
                $imagePath = $destination;
                // Delete old image if exists
                if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                    unlink($_POST['current_image']);
                }
            }
        } elseif (!empty($_POST['current_image'])) {
            $imagePath = $_POST['current_image'];
        }

        if (isset($_POST['delete'])) {
            // Delete artwork
            $stmt = $pdo->prepare("DELETE FROM oeuvres WHERE id = ? AND id_employe = ?");
            $stmt->execute([$_POST['artwork_id'], $_SESSION['user_id']]);
            
            // Delete the associated image file
            if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                unlink($_POST['current_image']);
            }
            
            $success = "Artwork deleted successfully!";
        } elseif (isset($_POST['update'])) {
            // Update artwork
            $stmt = $pdo->prepare("UPDATE oeuvres SET 
                titre = ?, artiste = ?, date_creation = ?, type_oeuvre = ?, 
                materiaux = ?, informations = ?, image_oeuvre = ?
                WHERE id = ? AND id_employe = ?");
            
            $stmt->execute([
                $_POST['titre'],
                $_POST['artiste'],
                $_POST['date_creation'],
                $_POST['type_oeuvre'],
                $_POST['materiaux'],
                $_POST['informations'],
                $imagePath,
                $_POST['artwork_id'],
                $_SESSION['user_id']
            ]);
            
            $success = "Artwork updated successfully!";
        } else {
            // Add new artwork
            $stmt = $pdo->prepare("INSERT INTO oeuvres 
                (titre, artiste, date_creation, type_oeuvre, materiaux, informations, image_oeuvre, id_employe) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $_POST['titre'],
                $_POST['artiste'],
                $_POST['date_creation'],
                $_POST['type_oeuvre'],
                $_POST['materiaux'],
                $_POST['informations'],
                $imagePath,
                $_SESSION['user_id']
            ]);
            
            $success = "Artwork added successfully!";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM oeuvres WHERE id = ? AND id_employe = ?");
        $stmt->execute([$_GET['edit'], $_SESSION['user_id']]);
        $artwork = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($artwork) {
            $edit_mode = true;
            $current_image = $artwork['image_oeuvre'];
        } else {
            $error = "Artwork not found or you don't have permission to edit it";
        }
    } catch (PDOException $e) {
        $error = "Error fetching artwork: " . $e->getMessage();
    }
}

// Fetch all artworks added by this employee
$artworks = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM oeuvres WHERE id_employe = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching artworks: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oeuvres Dashboard - Time Travel Museum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        .artwork-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .artwork-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .artwork-image {
            height: 200px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }
        .artwork-details {
            padding: 15px;
        }
        .artwork-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .artwork-artist {
            color: #666;
            margin-bottom: 10px;
        }
        
        /* Add these new styles */
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
        .delete-btn {
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
                <p>Oeuvres Management</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="Eoeuvres-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#card"><i class="fas fa-list"></i> All Artworks</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><?php echo $edit_mode ? 'Edit Artwork' : 'Add New Artwork'; ?></h1>
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
                <form action="Eoeuvres-dashboard.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="artwork_id" value="<?php echo $artwork['id']; ?>">
                        <input type="hidden" name="update" value="1">
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($artwork['image_oeuvre']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="titre">Title*</label>
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($artwork['titre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="artiste">Artist*</label>
                        <input type="text" id="artiste" name="artiste" value="<?php echo htmlspecialchars($artwork['artiste']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_creation">Creation Date</label>
                        <input type="date" id="date_creation" name="date_creation" value="<?php echo htmlspecialchars($artwork['date_creation']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="type_oeuvre">Art Type*</label>
                        <select id="type_oeuvre" name="type_oeuvre" required>
                            <option value="">Select type</option>
                            <option value="Painting" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Painting' ? 'selected' : ''; ?>>Painting</option>
                            <option value="Sculpture" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Sculpture' ? 'selected' : ''; ?>>Sculpture</option>
                            <option value="Photography" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Photography' ? 'selected' : ''; ?>>Photography</option>
                            <option value="Digital Art" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Digital Art' ? 'selected' : ''; ?>>Digital Art</option>
                            <option value="Installation" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Installation' ? 'selected' : ''; ?>>Installation</option>
                            <option value="Other" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="materiaux">Materials</label>
                        <input type="text" id="materiaux" name="materiaux" value="<?php echo htmlspecialchars($artwork['materiaux']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="informations">Description</label>
                        <textarea id="informations" name="informations"><?php echo htmlspecialchars($artwork['informations']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_oeuvre">Artwork Image</label>
                        <input type="file" id="image_oeuvre" name="image_oeuvre" accept="image/*">
                        <?php if ($edit_mode && !empty($artwork['image_oeuvre'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($artwork['image_oeuvre']); ?>" class="current-image">
                                <p>Current image</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn">
                        <?php echo $edit_mode ? 'Update Artwork' : 'Add Artwork'; ?>
                    </button>
                    
                    <?php if ($edit_mode): ?>
                        <a href="Eoeuvres-dashboard.php" class="btn" style="background: #666; margin-left: 10px;">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <h2>Your Added Artworks</h2>
            <?php if (empty($artworks)): ?>
                <p>No artworks added yet.</p>
            <?php else: ?>
                <div class="artwork-grid">
                    <?php foreach ($artworks as $art): ?>
                        <div class="artwork-card">
                            <div class="artwork-image" style="background-image: url('<?php echo htmlspecialchars($art['image_oeuvre'] ?? 'https://via.placeholder.com/300x200?text=No+Image'); ?>')"></div>
                            <div class="artwork-details">
                                <div class="artwork-title"><?php echo htmlspecialchars($art['titre']); ?></div>
                                <div class="artwork-artist"><?php echo htmlspecialchars($art['artiste']); ?></div>
                                <div><?php echo htmlspecialchars($art['type_oeuvre']); ?></div>
                                <div><?php echo htmlspecialchars($art['date_creation']); ?></div>
                                <div class="action-buttons">
                                    <a href="Eoeuvres-dashboard.php?edit=<?php echo $art['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <form action="Eoeuvres-dashboard.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="artwork_id" value="<?php echo $art['id']; ?>">
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($art['image_oeuvre']); ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this artwork?')">Delete</button>
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