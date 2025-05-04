<?php
session_start();
require 'config.php';

// Verify admin or oeuvres role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'oeuvres')) {
  header('Location: login.php');
  exit();
}

$artworks = [];
$error = '';

try {
  // Show all artworks for both admin and oeuvres roles
  if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'oeuvres') {
    $stmt = $pdo->prepare("SELECT * FROM oeuvres ORDER BY id DESC");
    $stmt->execute();
  } else {
    // Other roles only see their own artworks (though this case shouldn't happen due to the role check above)
    $stmt = $pdo->prepare("SELECT * FROM oeuvres WHERE id_employe = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
  }
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
    <div class="flex-1 p-5">
      <div class="flex justify-between items-center mb-5">
        <h1 class="text-2xl font-bold">Your Added Artworks</h1>
        <a href="collection-form.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
          Add New Artwork
        </a>
      </div>
      
      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <div class="space-y-6">
        <?php if (empty($artworks)): ?>
          <!-- No Artworks Message -->
          <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500 text-lg">There are no artworks yet.</p>
            <p class="text-gray-400 mt-2">Click "Add New Artwork" to get started.</p>
          </div>
        <?php else: ?>
          <!-- Artworks Grid -->
          <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($artworks as $art): ?>
              <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="h-48 w-full relative">
                  <img
                    src="<?php echo htmlspecialchars($art['image_oeuvre'] ?? 'https://via.placeholder.com/300x200?text=No+Image'); ?>"
                    alt="<?php echo htmlspecialchars($art['titre']); ?>"
                    class="w-full h-full object-cover" />
                </div>
                <div class="p-4">
                  <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($art['titre']); ?></h3>
                  <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($art['artiste']); ?></p>
                  <div class="mt-4 flex space-x-2">
                    <a href="collection-form.php?edit=<?php echo $art['id']; ?>"
                      class="flex-1 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 text-center">
                      Edit
                    </a>
                    <form action="collection-form.php" method="POST" class="inline">
                      <input type="hidden" name="artwork_id" value="<?php echo $art['id']; ?>">
                      <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($art['image_oeuvre']); ?>">
                      <input type="hidden" name="delete" value="1">
                      <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-md hover:bg-gray-100"
                        onclick="return confirm('Are you sure you want to delete this artwork?')">
                        Delete
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>