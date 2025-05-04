<?php
session_start();
require 'config.php';

// Authorization check - allow both admin and event staff
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'evenements' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// Initialize variables with default values
$success = $error = '';
$event = [
    'id' => '',
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

        if (isset($_POST['delete'])) {
            // Delete event
            $stmt = $pdo->prepare("DELETE FROM evenement WHERE id = ?");
            $stmt->execute([$_POST['event_id']]);
            
            // Delete the associated image file
            if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                unlink($_POST['current_image']);
            }
            
            $success = "Event deleted successfully!";
            header('Location: events-list.php');
            exit();
        } elseif (isset($_POST['update'])) {
            // Update event
            $stmt = $pdo->prepare("UPDATE evenement SET 
                titre = ?, description = ?, date_debut = ?, date_fin = ?, 
                lieu = ?, capacite = ?, image_evenement = ?
                WHERE id = ?");
            
            $stmt->execute([
                $_POST['titre'],
                $_POST['description'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['lieu'],
                $_POST['capacite'],
                $imagePath,
                $_POST['event_id']
            ]);
            
            $success = "Event updated successfully!";
            header('Location: events-list.php');
            exit();
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
            header('Location: events-list.php');
            exit();
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM evenement WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            $edit_mode = true;
            $current_image = $event['image_evenement'] ?? '';
        } else {
            $error = "Event not found";
        }
    } catch (PDOException $e) {
        $error = "Error fetching event: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - Time Travel Museum</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-5">
            <div class="flex justify-between items-center mb-5">
                <h1 class="text-2xl font-bold"><?php echo $edit_mode ? 'Edit Event' : 'Add New Event'; ?></h1>
            </div>

            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow p-5 mb-5" id="card">
                <form action="events-form.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                        <input type="hidden" name="update" value="1">
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($current_image ?? ''); ?>">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="titre" class="block font-medium mb-1">Title*</label>
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($event['titre'] ?? ''); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block font-medium mb-1">Description*</label>
                        <textarea id="description" name="description" 
                                  class="w-full p-2 border border-gray-300 rounded h-24" required><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="date_debut" class="block font-medium mb-1">Start Date*</label>
                        <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($event['date_debut'] ?? ''); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="date_fin" class="block font-medium mb-1">End Date*</label>
                        <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($event['date_fin'] ?? ''); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="lieu" class="block font-medium mb-1">Location*</label>
                        <input type="text" id="lieu" name="lieu" value="<?php echo htmlspecialchars($event['lieu'] ?? ''); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="capacite" class="block font-medium mb-1">Capacity*</label>
                        <input type="number" id="capacite" name="capacite" value="<?php echo htmlspecialchars($event['capacite'] ?? ''); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required min="1">
                    </div>
                    
                    <div class="mb-4">
                        <label for="image_evenement" class="block font-medium mb-1">Event Image</label>
                        <input type="file" id="image_evenement" name="image_evenement" accept="image/*" 
                               class="w-full p-2 border border-gray-300 rounded">
                        <?php if ($edit_mode && !empty($current_image)): ?>
                            <div class="mt-2">
                                <img src="<?php echo htmlspecialchars($current_image); ?>" class="max-w-[200px] max-h-[150px]">
                                <p class="text-sm text-gray-500">Current image</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                        <?php echo $edit_mode ? 'Update Event' : 'Add Event'; ?>
                    </button>
                    
                    <?php if ($edit_mode): ?>
                        <button type="submit" name="delete" 
                                class="ml-2 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                                onclick="return confirm('Are you sure you want to delete this event?')">
                            Delete Event
                        </button>
                        <a href="events-list.php" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>