<?php
session_start();
require 'config.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'visiteurs' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$success = $error = '';
$visitor = [
    'email' => '',
    'mot_de_passe' => '',
    'tel' => '',
    'age' => '',
    'nom' => '',
    'prenom' => '',
    'type_visiteur' => 'regular' // default value
];
$edit_mode = false;
$password_required = true;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete'])) {
            // Delete visitor
            $stmt = $pdo->prepare("DELETE FROM visiteur WHERE id = ?");
            $stmt->execute([$_POST['visitor_id']]);
            $success = "Visitor deleted successfully!";
        } elseif (isset($_POST['update'])) {
            // Update visitor - password is optional during update
            if (!empty($_POST['mot_de_passe'])) {
                $hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE visiteur SET 
                    email = ?, mot_de_passe = ?, tel = ?, age = ?, 
                    nom = ?, prenom = ?, type_visiteur = ?
                    WHERE id = ?");
                
                $stmt->execute([
                    $_POST['email'],
                    $hashed_password,
                    $_POST['tel'],
                    $_POST['age'],
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['type_visiteur'],
                    $_POST['visitor_id']
                ]);
            } else {
                // Don't update password if field is empty
                $stmt = $pdo->prepare("UPDATE visiteur SET 
                    email = ?, tel = ?, age = ?, 
                    nom = ?, prenom = ?, type_visiteur = ?
                    WHERE id = ?");
                
                $stmt->execute([
                    $_POST['email'],
                    $_POST['tel'],
                    $_POST['age'],
                    $_POST['nom'],
                    $_POST['prenom'],
                    $_POST['type_visiteur'],
                    $_POST['visitor_id']
                ]);
            }
            
            $success = "Visitor updated successfully!";
        } else {
            // Add new visitor - password is required
            $hashed_password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO visiteur 
                (email, mot_de_passe, tel, age, nom, prenom, type_visiteur) 
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
            $password_required = false; // Password not required when editing
        } else {
            $error = "Visitor not found";
        }
    } catch (PDOException $e) {
        $error = "Error fetching visitor: " . $e->getMessage();
    }
}

// Handle search
$search_results = [];
if (isset($_GET['search'])) {
    try {
        $search_term = '%' . $_GET['search'] . '%';
        $stmt = $pdo->prepare("SELECT * FROM visiteur 
                             WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR id = ?
                             ORDER BY nom, prenom");
        $stmt->execute([$search_term, $search_term, $search_term, $_GET['search']]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Search error: " . $e->getMessage();
    }
}

// Fetch all visitors
$visitors = [];
try {
    $stmt = $pdo->query("SELECT id, email, nom, prenom, age, type_visiteur FROM visiteur ORDER BY nom, prenom");
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching visitors: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Dashboard - Time Travel Museum</title>
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
                        secondary: {
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        regular: {
                            bg: '#e3f2fd',
                            text: '#1976d2'
                        },
                        vip: {
                            bg: '#e8f5e9',
                            text: '#388e3c'
                        },
                        staff: {
                            bg: '#f3e5f5',
                            text: '#8e24aa'
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
                <p class="text-gray-400 text-sm">Visitors Management</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="Evisiteurs-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg bg-dark-800 text-white">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#card" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
                    <i class="fas fa-list w-5 text-center"></i>
                    <span>All Visitors</span>
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
                <h1 class="text-2xl font-bold text-gray-800">
                    <?php echo $edit_mode ? 'Edit Visitor' : 'Add New Visitor'; ?>
                </h1>
                <div class="text-gray-600">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                    <span class="bg-primary-600 text-white px-2 py-1 rounded text-sm">
                        <?php echo htmlspecialchars($_SESSION['role']); ?>
                    </span>
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

            <div class="bg-white rounded-lg shadow p-6 mb-8" id="card">
                <form action="Evisiteurs-dashboard.php" method="POST">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="visitor_id" value="<?php echo $visitor['id']; ?>">
                        <input type="hidden" name="update" value="1">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($visitor['nom']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($visitor['prenom']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($visitor['email']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    
                    <div class="mb-6">
                        <label for="mot_de_passe" class="block text-sm font-medium text-gray-700 mb-1">Password<?php echo $password_required ? '*' : ''; ?></label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" <?php echo $password_required ? 'required' : ''; ?>>
                        <?php if (!$password_required): ?>
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="tel" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="tel" name="tel" value="<?php echo htmlspecialchars($visitor['tel']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age*</label>
                            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($visitor['age']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required min="0">
                        </div>
                        <div>
                            <label for="type_visiteur" class="block text-sm font-medium text-gray-700 mb-1">Visitor Type*</label>
                            <select id="type_visiteur" name="type_visiteur" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" required>
                                <option value="regular" <?php echo ($visitor['type_visiteur'] ?? '') === 'regular' ? 'selected' : ''; ?>>Regular</option>
                                <option value="vip" <?php echo ($visitor['type_visiteur'] ?? '') === 'vip' ? 'selected' : ''; ?>>VIP</option>
                                <option value="staff" <?php echo ($visitor['type_visiteur'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <?php echo $edit_mode ? 'Update Visitor' : 'Add Visitor'; ?>
                        </button>
                        
                        <?php if ($edit_mode): ?>
                            <a href="Evisiteurs-dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Search Box -->
            <div class="mb-8">
                <form action="Evisiteurs-dashboard.php" method="GET" class="flex space-x-2">
                    <input type="text" name="search" placeholder="Search by name, email or ID" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Search
                    </button>
                    <?php if (isset($_GET['search'])): ?>
                        <a href="Evisiteurs-dashboard.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-4">Visitor Records</h2>
            <?php 
            $display_visitors = isset($_GET['search']) ? $search_results : $visitors;
            if (empty($display_visitors)): ?>
                <p class="text-gray-600">No visitors found.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($display_visitors as $vis): ?>
                        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                        <div class="visitor-name"><?php echo htmlspecialchars($vis['prenom'] . ' ' . $vis['nom']); ?></div>
                            <div class="text-gray-600 text-sm mb-1"><?php echo htmlspecialchars($vis['email']); ?></div>
                            <div class="font-mono bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded inline-block mb-2">
                                ID: <?php echo htmlspecialchars($vis['id']); ?>
                            </div>
                            <div class="text-sm mb-2">Age: <?php echo htmlspecialchars($vis['age']); ?></div>
                            <div class="text-xs px-2 py-1 rounded-full inline-block mb-3 
                                <?php echo $vis['type_visiteur'] === 'regular' ? 'bg-regular-bg text-regular-text' : ''; ?>
                                <?php echo $vis['type_visiteur'] === 'vip' ? 'bg-vip-bg text-vip-text' : ''; ?>
                                <?php echo $vis['type_visiteur'] === 'staff' ? 'bg-staff-bg text-staff-text' : ''; ?>">
                                <?php echo htmlspecialchars(ucfirst($vis['type_visiteur'])); ?>
                            </div>
                            <div class="flex space-x-2">
                                <a href="Evisiteurs-dashboard.php?edit=<?php echo $vis['id']; ?>" 
                                   class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Edit
                                </a>
                                <form action="Evisiteurs-dashboard.php" method="POST" class="inline">
                                    <input type="hidden" name="visitor_id" value="<?php echo $vis['id']; ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <button type="submit" 
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            onclick="return confirm('Are you sure you want to delete this visitor?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>