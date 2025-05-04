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
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     try {
//         if (isset($_POST['delete_employee'])) {
//             // Prevent admin from deleting themselves
//             if ($_POST['employee_id'] != $_SESSION['user_id']) {
//                 $stmt = $pdo->prepare("DELETE FROM employe WHERE id = ?");
//                 $stmt->execute([$_POST['employee_id']]);
//                 $success = "Employee deleted successfully!";
//             } else {
//                 $error = "You cannot delete your own account!";
//             }
//         } elseif (isset($_POST['update_employee'])) {
//             // Update employee - password is optional
//             if (!empty($_POST['password'])) {
//                 $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
//                 $stmt = $pdo->prepare("UPDATE employe SET 
//                     username = ?, email = ?, role = ?, password = ?
//                     WHERE id = ?");
//                 $stmt->execute([
//                     $_POST['username'],
//                     $_POST['email'],
//                     $_POST['role'],
//                     $hashed_password,
//                     $_POST['employee_id']
//                 ]);
//             } else {
//                 $stmt = $pdo->prepare("UPDATE employe SET 
//                     username = ?, email = ?, role = ?
//                     WHERE id = ?");
//                 $stmt->execute([
//                     $_POST['username'],
//                     $_POST['email'],
//                     $_POST['role'],
//                     $_POST['employee_id']
//                 ]);
//             }
//             $success = "Employee updated successfully!";
//         } elseif (isset($_POST['add_employee'])) {
//             // Add new employee with provided password
//             if (empty($_POST['password'])) {
//                 $error = "Password is required when adding a new employee";
//             } else {
//                 $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
//                 $stmt = $pdo->prepare("INSERT INTO employe 
//                     (username, email, password, role) 
//                     VALUES (?, ?, ?, ?)");
//                 $stmt->execute([
//                     $_POST['username'],
//                     $_POST['email'],
//                     $hashed_password,
//                     $_POST['role']
//                 ]);
//                 $success = "Employee added successfully!";
//             }
//         }
//     } catch (PDOException $e) {
//         $error = "Database error: " . $e->getMessage();
//     }
// }

// // Fetch all employees
// $employees = [];
// try {
//     $stmt = $pdo->query("SELECT * FROM employe ORDER BY role, username");
//     $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     $error = "Error fetching employees: " . $e->getMessage();
// }
// 
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
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php
                    $cards = [
                        ['title' => 'Total Visitors', 'value' => '12,486', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>', 'change' => ['value' => '12%', 'positive' => true], 'color' => 'blue'],
                        ['title' => 'Ticket Sales', 'value' => '$24,350', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg>', 'change' => ['value' => '8.5%', 'positive' => true], 'color' => 'green'],
                        ['title' => 'Replica Sales', 'value' => '$8,294', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M15.5 14h-.79l-1.2-1.2c.49-.78.79-1.68.79-2.64a6.5 6.5 0 1 0-13 0c0 .96.29 1.86.79 2.64L5.21 14H4.5A1.5 1.5 0 0 0 3 15.5V18a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 19 18v-2.5a1.5 1.5 0 0 0-1.5-1.5zM9 14c0-2.21 1.79-4 4-4s4 1.79 4 4v2H9v-2zm7.5 5.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 5.5 18v-2.5a1.5 1.5 0 0 1 1.5-1.5h13a1.5 1.5 0 0 1 1.5 1.5V18z"/></svg>', 'change' => ['value' => '5.2%', 'positive' => true], 'color' => 'purple'],
                    ];
                    foreach ($cards as $card):
                    ?>
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-<?php echo $card['color']; ?>-100 text-<?php echo $card['color']; ?>-600 mr-4">
                                    <?php echo $card['icon']; ?>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500"><?php echo $card['title']; ?></h3>
                                    <p class="text-2xl font-semibold text-gray-800"><?php echo $card['value']; ?></p>
                                    <p class="mt-1 text-sm <?php echo $card['change']['positive'] ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo $card['change']['value']; ?> from last month
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Visitor Traffic</h3>
                        <p class="text-sm text-gray-500 mb-4">Last 30 days</p>
                        <div class="flex h-32 items-end space-x-1">
                            <?php for ($i = 0; $i < 14; $i++):
                                $height = 30 + rand(0, 70); // Random height between 30% - 100%
                            ?>
                                <div class="relative flex-1 group">
                                    <div style="height:<?php echo $height; ?>%" class="absolute inset-x-0 bottom-0 bg-indigo-500 rounded-t-sm transition-all duration-300 ease-in-out hover:bg-indigo-600 group-hover:opacity-80"></div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 h-full flex flex-col">
  <div>
    <h3 class="text-lg font-semibold text-gray-800">Revenue</h3>
    <p class="text-sm text-gray-500 mb-4">Tickets vs Replicas</p>
  </div>
  
  <div class="flex-grow flex flex-col sm:flex-row gap-4">
    <!-- Chart Section -->
    <div class="sm:w-1/2 flex flex-col items-center justify-center">
      <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4">
        <!-- Chart rings -->
        <div class="absolute inset-0 rounded-full border-8 border-indigo-200"></div>
        <div class="absolute inset-0 rounded-full border-8 border-indigo-500 border-t-transparent border-r-transparent border-b-0" style="transform: rotate(45deg)"></div>
        <div class="absolute inset-0 flex items-center justify-center">
          <span class="text-lg font-semibold text-gray-800">75%</span>
        </div>
      </div>
    </div>

    <!-- Data Bars Section -->
    <div class="sm:w-1/2 flex flex-col justify-center">
      <div class="space-y-4">
        <!-- Tickets -->
        <div>
          <div class="flex justify-between text-sm mb-1">
            <span class="font-medium">Tickets</span>
            <span>$24,350</span>
          </div>
          <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-500 rounded-full" style="width: 75%;"></div>
          </div>
        </div>

        <!-- Replicas -->
        <div>
          <div class="flex justify-between text-sm mb-1">
            <span class="font-medium">Replicas</span>
            <span>$8,294</span>
          </div>
          <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-purple-500 rounded-full" style="width: 25%;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="bg-white rounded-lg shadow-sm lg:col-span-1">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800">Upcoming Events</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <?php
                            $events = [
                                ['title' => 'Renaissance Exhibition', 'date' => 'May 15, 2025', 'attendees' => 86],
                                ['title' => 'Modern Art Workshop', 'date' => 'May 18, 2025', 'attendees' => 42],
                                ['title' => 'Ancient Artifacts Tour', 'date' => 'May 22, 2025', 'attendees' => 120],
                            ];
                            foreach ($events as $event):
                            ?>
                                <div class="p-4 transition-colors hover:bg-gray-50">
                                    <div class="flex items-start">
                                        <div class="p-2 bg-indigo-100 rounded-md text-indigo-600 mr-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-16H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800"><?php echo $event['title']; ?></h4>
                                            <div class="flex items-center mt-1">
                                                <span class="text-sm text-gray-500 mr-3"><?php echo $event['date']; ?></span>
                                                <span class="text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full">
                                                    <?php echo $event['attendees']; ?> attendees
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="p-4">
                            <button class="w-full py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors">
                                View All Events
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm lg:col-span-2">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Reviews</h3>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor" class="text-amber-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                <span class="ml-1 text-lg font-semibold">4.8</span>
                                <span class="ml-1 text-sm text-gray-500">average</span>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <?php
                            $reviews = [
                                ['name' => 'Emma Wilson', 'rating' => 5, 'comment' => 'Absolutely loved the new Renaissance exhibition. The audio guide was very informative!', 'time' => '2 hours ago', 'exhibit' => 'Renaissance Exhibition'],
                                ['name' => 'Michael Chen', 'rating' => 4, 'comment' => 'Great collection of artifacts. Would recommend the guided tour for a better experience.', 'time' => '5 hours ago', 'exhibit' => 'Ancient Artifacts'],
                                ['name' => 'Sophia Rodriguez', 'rating' => 5, 'comment' => 'The interactive displays were amazing! My kids loved the experience.', 'time' => '1 day ago', 'exhibit' => 'Modern Art Exhibition'],
                            ];
                            foreach ($reviews as $review):
                            ?>
                                <div class="p-4 transition-colors hover:bg-gray-50">
                                    <div class="flex justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                <?php echo substr($review['name'], 0, 1); ?>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="font-medium text-gray-800"><?php echo $review['name']; ?></h4>
                                                <div class="flex items-center mt-0.5">
                                                    <div class="flex mr-2">
                                                        <?php for ($r = 0; $r < 5; $r++): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="<?php echo $r < $review['rating'] ? 'text-amber-400' : 'text-gray-300'; ?>" viewBox="0 0 24 24" stroke="currentColor" fill="<?php echo $r < $review['rating'] ? 'currentColor' : 'none'; ?>">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                            </svg>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="text-xs text-gray-500"><?php echo $review['time']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full h-fit">
                                            <?php echo $review['exhibit']; ?>
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600"><?php echo $review['comment']; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="p-4">
                            <button class="w-full py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors">
                                View All Reviews
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Popular Exhibits</h3>
                        <span class="text-sm text-gray-500">Based on visitor traffic</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exhibit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitors</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $exhibits = [
                                    ['name' => 'Renaissance Masterpieces', 'category' => 'Fine Art', 'visitors' => '3,245', 'rating' => 4.8, 'trend' => 'up'],
                                    ['name' => 'Ancient Egyptian Artifacts', 'category' => 'Historical', 'visitors' => '2,876', 'rating' => 4.7, 'trend' => 'up'],
                                    ['name' => 'Modern Art Collection', 'category' => 'Contemporary', 'visitors' => '2,432', 'rating' => 4.5, 'trend' => 'down'],
                                    ['name' => 'Dinosaur Fossils', 'category' => 'Natural History', 'visitors' => '2,145', 'rating' => 4.9, 'trend' => 'up'],
                                    ['name' => 'Cultural Heritage', 'category' => 'Anthropology', 'visitors' => '1,987', 'rating' => 4.6, 'trend' => 'down'],
                                ];

                                foreach ($exhibits as $exhibit):
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900"><?php echo $exhibit['name']; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                <?php echo $exhibit['category']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $exhibit['visitors']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="mr-2 font-medium"><?php echo $exhibit['rating']; ?></span>
                                                <div class="flex">
                                                    <?php for ($r = 0; $r < 5; $r++): ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" class="<?php echo $r < floor($exhibit['rating']) ? 'text-amber-400' : 'text-gray-300'; ?>" viewBox="0 0 24 24" stroke="currentColor" fill="<?php echo $r < floor($exhibit['rating']) ? 'currentColor' : 'none'; ?>">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                        </svg>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center <?php echo $exhibit['trend'] === 'up' ? 'text-green-600' : 'text-red-600'; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="<?php echo $exhibit['trend'] === 'up' ? '' : 'transform rotate-180'; ?>" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                <span class="ml-1 text-sm">
                                                    <?php echo $exhibit['trend'] === 'up' ? '+12%' : '-5%'; ?>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>