<?php
session_start();
require 'config.php';
// hello
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

// Fetch all employees
$employees = [];
try {
    $stmt = $pdo->query("SELECT * FROM employe ORDER BY role, username");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching employees: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Time Travel Museum</title>
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
        <div class="w-64 bg-dark-900 text-white flex flex-col">
            <div class="p-4 border-b border-dark-800">
                <h2 class="text-xl font-bold">Time Travel Museum</h2>
                <p class="text-gray-400 text-sm">Administration Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg bg-dark-800 text-white">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="admin-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg bg-dark-800 text-white">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Collections</span>
                </a>
                <a href="admin-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg bg-dark-800 text-white">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Events</span>
                </a>
                <a href="#employee-management" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-users-cog w-5 text-center"></i>
                    <span>Employees</span>
                </a>
                <a href="#employee-management" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-users-cog w-5 text-center"></i>
                    <span>Reviews</span>
                </a>
                <a href="#employee-management" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-users-cog w-5 text-center"></i>
                    <span>Orders</span>
                </a>
                <a href="#employee-management" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-users-cog w-5 text-center"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="text-gray-600">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <span class="bg-admin-bg text-admin-text text-xs px-2 py-1 rounded-full">Admin</span>
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

            <!-- Dashboard Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8">
                <a href="Eoeuvres-dashboard.php" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow flex flex-col items-center text-center">
                    <i class="fas fa-paint-brush text-3xl text-gray-600 mb-3"></i>
                    <span class="font-medium text-gray-800">Collections Management</span>
                </a>
                <a href="Eevenements-dashboard.php" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow flex flex-col items-center text-center">
                    <i class="fas fa-calendar-alt text-3xl text-gray-600 mb-3"></i>
                    <span class="font-medium text-gray-800">Events Management</span>
                </a>
                <a href="Evisiteurs-dashboard.php" class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow flex flex-col items-center text-center">
                    <i class="fas fa-users text-3xl text-gray-600 mb-3"></i>
                    <span class="font-medium text-gray-800">Visitors Management</span>
                </a>
            </div>

            <!-- Employee Form -->
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

            <!-- Employee List -->
            <h2 class="text-xl font-bold text-gray-800 mb-4">Employee Management</h2>
            <?php if (empty($employees)): ?>
                <p class="text-gray-600">No employees found.</p>
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
                                    <a href="admin-dashboard.php?edit_employee=<?php echo $emp['id']; ?>"
                                        class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Edit
                                    </a>
                                    <form action="admin-dashboard.php" method="POST" class="inline">
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
</body>

</html>