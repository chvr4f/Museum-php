<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'visiteurs' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

$success = $error = '';
$visitor = [
    'id' => '',
    'email' => '',
    'password' => '',
    'tel' => '',
    'age' => '',
    'nom' => '',
    'prenom' => '',
    'type_visiteur' => 'regular'
];
$edit_mode = false;
$password_required = true;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete'])) {
            // Delete visitor
            $stmt = $pdo->prepare("DELETE FROM visiteur WHERE id = ?");
            $stmt->execute([$_POST['visitor_id']]);
            $success = "Visitor deleted successfully!";
            header('Location: visitors-list.php');
            exit();
        } elseif (isset($_POST['update'])) {
            // Update visitor
            $params = [
                $_POST['email'],
                $_POST['tel'],
                $_POST['age'],
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['type_visiteur'],
                $_POST['visitor_id']
            ];

            if (!empty($_POST['mot_de_passe'])) {
                $hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE visiteur SET 
                    email = ?, password = ?, tel = ?, age = ?, 
                    nom = ?, prenom = ?, type_visiteur = ?
                    WHERE id = ?");
                array_splice($params, 1, 0, [$hashed_password]); // Insert password into correct position
            } else {
                $stmt = $pdo->prepare("UPDATE visiteur SET 
                    email = ?, tel = ?, age = ?, 
                    nom = ?, prenom = ?, type_visiteur = ?
                    WHERE id = ?");
            }

            $stmt->execute($params);
            $success = "Visitor updated successfully!";
            header('Location: visitors-list.php');
            exit();
        } else {
            // Add new visitor
            if (empty($_POST['mot_de_passe'])) {
                $error = "Password is required for new visitors.";
            } else {
                $hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO visiteur 
                    (email, password, tel, age, nom, prenom, type_visiteur) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $_POST['email'],
                    $hashed_password,
                    $_POST['tel'],
                    $_POST['age'],
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['type_visiteur']
                ]);
                $success = "Visitor added successfully!";
                header('Location: visitors-list.php');
                exit();
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM visiteur WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($visitor) {
            $edit_mode = true;
            $password_required = false; 
        } else {
            $error = "Visitor not found";
        }
    } catch (PDOException $e) {
        $error = "Error fetching visitor: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visitors - Time Travel Museum</title>
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
        <h1 class="text-2xl font-bold"><?php echo $edit_mode ? 'Edit Visitor' : 'Add New Visitor'; ?></h1>
        <div class="text-gray-700">
          Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
          (<?php echo htmlspecialchars($_SESSION['role']); ?>)
        </div>
      </div>

      <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-5">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>

      <div class="bg-white rounded-lg shadow p-5 mb-5" id="card">
        <form action="visitors-form.php" method="POST">
          <?php if ($edit_mode): ?>
            <input type="hidden" name="visitor_id" value="<?php echo htmlspecialchars($visitor['id']); ?>">
            <input type="hidden" name="update" value="1">
          <?php endif; ?>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="nom" class="block font-medium mb-1">Last Name*</label>
              <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($visitor['nom']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required>
            </div>
            <div>
              <label for="prenom" class="block font-medium mb-1">First Name*</label>
              <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($visitor['prenom']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required>
            </div>
          </div>

          <div class="mb-4">
            <label for="email" class="block font-medium mb-1">Email*</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($visitor['email']); ?>" 
                   class="w-full p-2 border border-gray-300 rounded" required>
          </div>

          <div class="mb-4">
            <label for="mot_de_passe" class="block font-medium mb-1">Password<?php echo $password_required ? '*' : ''; ?></label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" 
                   class="w-full p-2 border border-gray-300 rounded" <?php echo $password_required ? 'required' : ''; ?>>
            <?php if (!$password_required): ?>
              <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
            <?php endif; ?>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="tel" class="block font-medium mb-1">Phone</label>
              <input type="tel" id="tel" name="tel" value="<?php echo htmlspecialchars($visitor['tel']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded">
            </div>
            <div>
              <label for="age" class="block font-medium mb-1">Age*</label>
              <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($visitor['age']); ?>" 
                     class="w-full p-2 border border-gray-300 rounded" required min="0">
            </div>
          </div>

          <div class="mb-4">
            <label for="type_visiteur" class="block font-medium mb-1">Visitor Type*</label>
            <select id="type_visiteur" name="type_visiteur" class="w-full p-2 border border-gray-300 rounded" required>
              <option value="regular" <?php echo ($visitor['type_visiteur'] ?? '') === 'regular' ? 'selected' : ''; ?>>Regular</option>
              <option value="vip" <?php echo ($visitor['type_visiteur'] ?? '') === 'vip' ? 'selected' : ''; ?>>VIP</option>
              <option value="staff" <?php echo ($visitor['type_visiteur'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
            </select>
          </div>

          <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
            <?php echo $edit_mode ? 'Update Visitor' : 'Add Visitor'; ?>
          </button>

          <?php if ($edit_mode): ?>
            <a href="visitors-list.php" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</body>
</html>