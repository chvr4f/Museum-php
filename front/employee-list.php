<?php
session_start();
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: login.php');
  exit();
}

// Handle employee deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_employee'])) {
    try {
        // Prevent admin from deleting themselves
        if ($_POST['employee_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM employe WHERE id = ?");
            $stmt->execute([$_POST['employee_id']]);
            $_SESSION['success'] = "Employee deleted successfully!";
        } else {
            $_SESSION['error'] = "You cannot delete your own account!";
        }
        header('Location: employee-list.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting employee: " . $e->getMessage();
        header('Location: employee-list.php');
        exit();
    }
}

// Fetch all employees
$employees = [];
try {
  $stmt = $pdo->query("SELECT * FROM employe ORDER BY role, username");
  $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $error = "Error fetching employees: " . $e->getMessage();
}

// Display messages from session
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success']);
unset($_SESSION['error']);
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
        <h1 class="text-2xl font-bold">Employees List</h1>
        <a href="employee-form.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
          Add New Employee
        </a>
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
      
      <div class="space-y-6">
        <?php if (empty($employees)): ?>
          <!-- No Employees Message -->
          <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <p class="text-gray-500 text-lg">There are no employees yet.</p>
            <p class="text-gray-400 mt-2">Click "Add New Employee" to get started.</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($employees as $emp): ?>
              <div class="bg-white rounded-lg shadow p-4 border border-gray-200 flex items-start space-x-4">
                <!-- Profile Picture -->
                <img src="pics/default_pfp.webp" alt="Profile Picture" class="w-16 h-16 rounded-full object-cover">

                <!-- Card Content -->
                <div>
                  <div class="font-bold text-lg mb-1"><?php echo htmlspecialchars($emp['username']); ?></div>
                  <div class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($emp['email']); ?></div>

                  <div class="text-xs px-2 py-1 rounded-full inline-block mb-3 
                    <?php echo 'bg-' . $emp['role'] . '-bg text-' . $emp['role'] . '-text'; ?>">
                    <?php echo htmlspecialchars(ucfirst($emp['role'])); ?>
                  </div>

                  <div class="flex space-x-2">
                    <a href="employee-form.php?edit=<?php echo $emp['id']; ?>"
                      class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                      Edit
                    </a>
                    <form action="" method="POST" class="inline">
                      <input type="hidden" name="employee_id" value="<?php echo $emp['id']; ?>">
                      <input type="hidden" name="delete_employee" value="1">
                      <button type="submit"
                        class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        onclick="return confirm('Are you sure you want to delete this employee?')">
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