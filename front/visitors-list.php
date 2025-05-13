<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'visiteurs' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

$visitors = [];
$error = '';

try {
   
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->query("SELECT * FROM visiteur ORDER BY id DESC");
    } else {
        $stmt = $pdo->query("SELECT * FROM visiteur ORDER BY id DESC");
    }

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
        <h1 class="text-2xl font-bold">Visitor Management</h1>
        <a href="visitors-form.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
          Add New Visitor
        </a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
          <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Registered Visitors</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($visitors)): ?>
                  <?php foreach ($visitors as $vis): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-600">
                              <?= strtoupper(substr($vis['prenom'], 0, 1)) ?>
                            </span>
                          </div>
                          <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">
                              <?= htmlspecialchars($vis['prenom'] . ' ' . $vis['nom']) ?>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($vis['email']) ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                          <?= date('M j, Y', strtotime($vis['created_at'] ?? date('Y-m-d'))) ?>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="
                          px-2 py-1 text-xs rounded-full
                          <?= $vis['type_visiteur'] === 'regular' ? 'bg-blue-100 text-blue-800' : '' ?>
                          <?= $vis['type_visiteur'] === 'vip' ? 'bg-green-100 text-green-800' : '' ?>
                          <?= $vis['type_visiteur'] === 'staff' ? 'bg-purple-100 text-purple-800' : '' ?>">
                          <?= ucfirst(htmlspecialchars($vis['type_visiteur'])) ?>
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="visitors-form.php?edit=<?= $vis['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="visitors-form.php" method="POST" class="inline">
                          <input type="hidden" name="visitor_id" value="<?= $vis['id'] ?>">
                          <input type="hidden" name="delete" value="1">
                          <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this visitor?')">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No visitors found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
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
</html>