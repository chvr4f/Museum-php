<?php
session_start();
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'oeuvres')) {
  header('Location: login.php');
  exit();
}

$success = $error = '';
$artwork = [
    'id' => '',
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


// Handle form submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
      // Handle image upload
      $imagePath = null;
      if (isset($_FILES['image_oeuvre']) && $_FILES['image_oeuvre']['error'] === UPLOAD_ERR_OK) {
          $uploadDir = 'uploads/oeuvres/';
          if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
          $extension = pathinfo($_FILES['image_oeuvre']['name'], PATHINFO_EXTENSION);
          $filename = uniqid() . '.' . $extension;
          $destination = $uploadDir . $filename;
          if (move_uploaded_file($_FILES['image_oeuvre']['tmp_name'], $destination)) {
              $imagePath = $destination;
              if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                  unlink($_POST['current_image']);
              }
          }
      } elseif (!empty($_POST['current_image'])) {
          $imagePath = $_POST['current_image'];
      }

      if (isset($_POST['delete'])) {
          // Delete artwork
          $stmt = $pdo->prepare("DELETE FROM oeuvres WHERE id = ?");
          $stmt->execute([$_POST['artwork_id']]);
          if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
              unlink($_POST['current_image']);
          }
          $success = "Artwork deleted successfully!";
      } elseif (isset($_POST['update'])) {
          // Update artwork
          $stmt = $pdo->prepare("UPDATE oeuvres SET 
              titre = ?, artiste = ?, date_creation = ?, type_oeuvre = ?, 
              materiaux = ?, informations = ?, image_oeuvre = ?
              WHERE id = ?");
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

      header('Location: collection-list.php');
      exit();

  } catch (PDOException $e) {
      $error = "Database error: " . $e->getMessage();
  }
}

// Handle edit request (GET)
if (isset($_GET['edit'])) {
  try {
      $stmt = $pdo->prepare("SELECT * FROM oeuvres WHERE id = ?");
      $stmt->execute([$_GET['edit']]);
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Collection - Time Travel Museum</title>
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

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-8">
    <div class="bg-white rounded-lg shadow p-5 mb-5" id="card">
    <form action="collection-form.php" method="POST" enctype="multipart/form-data">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="artwork_id" value="<?php echo htmlspecialchars($artwork['id']); ?>">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($artwork['image_oeuvre']); ?>">
        <?php endif; ?>

        <!-- Title -->
        <div class="mb-4">
            <label for="titre" class="block font-medium mb-1">Title*</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($artwork['titre']); ?>" 
                   class="w-full p-2 border border-gray-300 rounded" required>
        </div>

        <!-- Artist -->
        <div class="mb-4">
            <label for="artiste" class="block font-medium mb-1">Artist*</label>
            <input type="text" id="artiste" name="artiste" value="<?php echo htmlspecialchars($artwork['artiste']); ?>" 
                   class="w-full p-2 border border-gray-300 rounded" required>
        </div>

        <!-- Creation Date -->
        <div class="mb-4">
            <label for="date_creation" class="block font-medium mb-1">Creation Date</label>
            <input type="date" id="date_creation" name="date_creation" value="<?php echo htmlspecialchars($artwork['date_creation']); ?>" 
                   class="w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- Art Type -->
        <div class="mb-4">
            <label for="type_oeuvre" class="block font-medium mb-1">Art Type*</label>
            <select id="type_oeuvre" name="type_oeuvre" class="w-full p-2 border border-gray-300 rounded" required>
                <option value="">Select type</option>
                <option value="Painting" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Painting' ? 'selected' : ''; ?>>Painting</option>
                <option value="Sculpture" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Sculpture' ? 'selected' : ''; ?>>Sculpture</option>
                <option value="Photography" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Photography' ? 'selected' : ''; ?>>Photography</option>
                <option value="Digital Art" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Digital Art' ? 'selected' : ''; ?>>Digital Art</option>
                <option value="Installation" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Installation' ? 'selected' : ''; ?>>Installation</option>
                <option value="Other" <?php echo ($artwork['type_oeuvre'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <!-- Materials -->
        <div class="mb-4">
            <label for="materiaux" class="block font-medium mb-1">Materials</label>
            <input type="text" id="materiaux" name="materiaux" value="<?php echo htmlspecialchars($artwork['materiaux']); ?>" 
                   class="w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="informations" class="block font-medium mb-1">Description</label>
            <textarea id="informations" name="informations" 
                      class="w-full p-2 border border-gray-300 rounded h-24"><?php echo htmlspecialchars($artwork['informations']); ?></textarea>
        </div>

        <!-- Image Upload -->
        <div class="mb-4">
            <label for="image_oeuvre" class="block font-medium mb-1">Artwork Image</label>
            <input type="file" id="image_oeuvre" name="image_oeuvre" accept="image/*" 
                   class="w-full p-2 border border-gray-300 rounded">
            <?php if ($edit_mode && !empty($artwork['image_oeuvre'])): ?>
                <div class="mt-2">
                    <img src="<?php echo htmlspecialchars($artwork['image_oeuvre']); ?>" class="max-w-[200px] max-h-[150px]">
                    <p class="text-sm text-gray-500">Current image</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
            <?php echo $edit_mode ? 'Update Artwork' : 'Add Artwork'; ?>
        </button>

        <!-- Cancel Button -->
        <?php if ($edit_mode): ?>
            <a href="collection-list.php" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
        <?php endif; ?>
    </form>
</div>
    </div>

  </div>
</body>

</html>