<?php
session_start();
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: login.php');
  exit();
}

// Initialize variables
$success = $error = '';
$employee = [
  'username' => '',
  'email' => '',
  'role' => 'staff'
];
$edit_mode = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['delete_employee'])) {
      // Prevent admin from deleting themselves
      if ($_POST['employee_id'] != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM employe WHERE id = ?");
        $stmt->execute([$_POST['employee_id']]);
        $success = "Employee deleted successfully!";
      } else {
        $error = "You cannot delete your own account!";
      }
    } elseif (isset($_POST['update_employee'])) {
      // Update employee - password is optional
      if (!empty($_POST['password'])) {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE employe SET 
                  username = ?, email = ?, role = ?, password = ?
                  WHERE id = ?");
        $stmt->execute([
          $_POST['username'],
          $_POST['email'],
          $_POST['role'],
          $hashed_password,
          $_POST['employee_id']
        ]);
      } else {
        $stmt = $pdo->prepare("UPDATE employe SET 
                  username = ?, email = ?, role = ?
                  WHERE id = ?");
        $stmt->execute([
          $_POST['username'],
          $_POST['email'],
          $_POST['role'],
          $_POST['employee_id']
        ]);
      }
      $success = "Employee updated successfully!";
    } elseif (isset($_POST['add_employee'])) {
      // Add new employee with provided password
      if (empty($_POST['password'])) {
        $error = "Password is required when adding a new employee";
      } else {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO employe 
                  (username, email, password, role) 
                  VALUES (?, ?, ?, ?)");
        $stmt->execute([
          $_POST['username'],
          $_POST['email'],
          $hashed_password,
          $_POST['role']
        ]);
        $success = "Employee added successfully!";
      }
    }
  } catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
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
    <div class="flex-1 p-5">
      <div class="bg-white rounded-lg shadow p-6 mb-8" id="employee-management">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
          <?php echo $edit_mode ? 'Edit Employee' : 'Add New Employee'; ?>
        </h2>
        <form action="admin-dashboard.php" method="POST">
          <?php if ($edit_mode): ?>
            <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
            <input type="hidden" name="update_employee" value="1">
          <?php else: ?>
            <input type="hidden" name="add_employee" value="1">
          <?php endif; ?>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username*</label>
              <input type="text" id="username" name="username"
                value="<?php echo htmlspecialchars($employee['username']); ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
              <input type="email" id="email" name="email"
                value="<?php echo htmlspecialchars($employee['email']); ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role*</label>
              <select id="role" name="role"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
                <option value="admin" <?php echo ($employee['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="oeuvres" <?php echo ($employee['role'] ?? '') === 'oeuvres' ? 'selected' : ''; ?>>Artworks Manager</option>
                <option value="evenements" <?php echo ($employee['role'] ?? '') === 'evenements' ? 'selected' : ''; ?>>Events Manager</option>
                <option value="visiteurs" <?php echo ($employee['role'] ?? '') === 'visiteurs' ? 'selected' : ''; ?>>Visitor Manager</option>
                <option value="staff" <?php echo ($employee['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
              </select>
            </div>
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password<?php echo $edit_mode ? '' : '*'; ?></label>
              <input type="password" id="password" name="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" <?php echo $edit_mode ? '' : 'required'; ?>>
              <?php if ($edit_mode): ?>
                <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
              <?php endif; ?>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              <?php echo $edit_mode ? 'Update Employee' : 'Add Employee'; ?>
            </button>
            <?php if ($edit_mode): ?>
              <a href="admin-dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
              </a>
            <?php endif; ?>
          </div>
        </form>
      </div>

    </div>

  </div>
</body>

</html>