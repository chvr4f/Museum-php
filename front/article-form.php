<?php
session_start();
require 'config.php';

// Verify admin role only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: login.php');
  exit();
}

$success = $error = '';
$article = [
    'id' => '',
    'nom' => '',
    'description' => '',
    'prix' => '',
    'quantite' => 0,
    'id_achat' => null,
    'image_article' => ''
];
$edit_mode = false;
$current_image = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
      // Handle image upload
      $imagePath = null;
      if (isset($_FILES['image_article']) && $_FILES['image_article']['error'] === UPLOAD_ERR_OK) {
          $uploadDir = 'uploads/articles/';
          if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
          $extension = pathinfo($_FILES['image_article']['name'], PATHINFO_EXTENSION);
          $filename = uniqid() . '.' . $extension;
          $destination = $uploadDir . $filename;
          if (move_uploaded_file($_FILES['image_article']['tmp_name'], $destination)) {
              $imagePath = $destination;
              if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                  unlink($_POST['current_image']);
              }
          }
      } elseif (!empty($_POST['current_image'])) {
          $imagePath = $_POST['current_image'];
      }

      // Validate required fields
      if (empty($_POST['nom']) || empty($_POST['prix']) || !is_numeric($_POST['prix']) || !is_numeric($_POST['quantite'])) {
          throw new Exception("Please fill all required fields with valid values");
      }

      if (isset($_POST['delete'])) {
          // Delete article
          $stmt = $pdo->prepare("DELETE FROM article WHERE id = ?");
          $stmt->execute([$_POST['article_id']]);
          if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
              unlink($_POST['current_image']);
          }
          $_SESSION['success'] = "Article deleted successfully!";
          header('Location: article-list.php');
          exit();
      } 
      
      if (isset($_POST['update'])) {
          // Update article
          $stmt = $pdo->prepare("UPDATE article SET 
              nom = ?, description = ?, prix = ?, quantite = ?, 
              id_achat = ?, image_article = ?
              WHERE id = ?");
          $stmt->execute([
              $_POST['nom'],
              $_POST['description'],
              $_POST['prix'],
              $_POST['quantite'],
              !empty($_POST['id_achat']) ? $_POST['id_achat'] : null,
              $imagePath,
              $_POST['article_id']
          ]);
          $_SESSION['success'] = "Article updated successfully!";
      } else {
          // Add new article
          $stmt = $pdo->prepare("INSERT INTO article 
              (nom, description, prix, quantite, id_achat, image_article) 
              VALUES (?, ?, ?, ?, ?, ?)");
          $stmt->execute([
              $_POST['nom'],
              $_POST['description'],
              $_POST['prix'],
              $_POST['quantite'],
              !empty($_POST['id_achat']) ? $_POST['id_achat'] : null,
              $imagePath
          ]);
          $_SESSION['success'] = "Article added successfully!";
      }

      header('Location: article-list.php');
      exit();

  } catch (Exception $e) {
      $error = $e->getMessage();
  }
}

// Handle edit request
if (isset($_GET['edit'])) {
  try {
      $stmt = $pdo->prepare("SELECT * FROM article WHERE id = ?");
      $stmt->execute([$_GET['edit']]);
      $article = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$article) {
          throw new Exception("Article not found");
      }
      $edit_mode = true;
      $current_image = $article['image_article'];
  } catch (PDOException $e) {
      $error = "Error fetching article: " . $e->getMessage();
  }
}

// Show success message from session
if (isset($_SESSION['success'])) {
  $success = $_SESSION['success'];
  unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Article - Time Travel Museum</title>
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
      
      <div class="bg-white rounded-lg shadow p-5 mb-5">
        <form action="article-form.php" method="POST" enctype="multipart/form-data">
          <?php if ($edit_mode): ?>
              <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article['id']); ?>">
              <input type="hidden" name="update" value="1">
              <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($article['image_article']); ?>">
          <?php endif; ?>

          <div class="mb-4">
              <label for="nom" class="block font-medium mb-1">Name*</label>
              <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($article['nom']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required>
          </div>

          <div class="mb-4">
              <label for="description" class="block font-medium mb-1">Description</label>
              <textarea id="description" name="description" 
                        class="w-full p-2 border border-gray-300 rounded h-24"><?php echo htmlspecialchars($article['description']); ?></textarea>
          </div>

          <div class="mb-4">
              <label for="prix" class="block font-medium mb-1">Price (€)*</label>
              <input type="number" id="prix" name="prix" step="0.01" min="0" 
                     value="<?php echo htmlspecialchars($article['prix']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required>
          </div>

          <div class="mb-4">
              <label for="quantite" class="block font-medium mb-1">Quantity*</label>
              <input type="number" id="quantite" name="quantite" min="0" 
                     value="<?php echo htmlspecialchars($article['quantite']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required>
          </div>

          <div class="mb-4">
              <label for="id_achat" class="block font-medium mb-1">Purchase ID (optional)</label>
              <input type="number" id="id_achat" name="id_achat" min="1"
                     value="<?php echo htmlspecialchars($article['id_achat']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded">
          </div>

          <div class="mb-4">
              <label for="image_article" class="block font-medium mb-1">Article Image</label>
              <input type="file" id="image_article" name="image_article" accept="image/*" 
                     class="w-full p-2 border border-gray-300 rounded">
              <?php if ($edit_mode && !empty($article['image_article'])): ?>
                  <div class="mt-2">
                      <img src="<?php echo htmlspecialchars($article['image_article']); ?>" class="max-w-[200px] max-h-[150px]">
                      <p class="text-sm text-gray-500">Current image</p>
                  </div>
              <?php endif; ?>
          </div>

          <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
              <?php echo $edit_mode ? 'Update Article' : 'Add Article'; ?>
          </button>

          <?php if ($edit_mode): ?>
              <a href="article-list.php" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</body>
</html>