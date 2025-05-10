<?php
session_start();
require 'config.php';

// Verify admin role only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$tickets = [];
$error = '';

try {
    // First, let's check what columns exist in your visiteur table
    $stmt = $pdo->query("SHOW COLUMNS FROM visiteur");
    $visitor_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Determine the correct visitor name column
    $visitor_name_column = 'nom'; // default assumption
    if (in_array('nom_visiteur', $visitor_columns)) {
        $visitor_name_column = 'nom_visiteur';
    } elseif (in_array('name', $visitor_columns)) {
        $visitor_name_column = 'name';
    } elseif (in_array('visitor_name', $visitor_columns)) {
        $visitor_name_column = 'visitor_name';
    }

    // Similarly for evenement table
    $stmt = $pdo->query("SHOW COLUMNS FROM evenement");
    $event_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $event_name_column = 'nom'; // default assumption
    if (in_array('titre', $event_columns)) {
        $event_name_column = 'titre';
    } elseif (in_array('name', $event_columns)) {
        $event_name_column = 'name';
    } elseif (in_array('event_name', $event_columns)) {
        $event_name_column = 'event_name';
    }

    // Now build the query with the correct column names
    $query = "SELECT b.*, 
              e.$event_name_column as evenement_nom, 
              v.$visitor_name_column as visiteur_nom 
              FROM billets b
              LEFT JOIN evenement e ON b.id_evenement = e.id
              LEFT JOIN visiteur v ON b.id_visiteur = v.id
              ORDER BY b.id DESC";
    
    $stmt = $pdo->query($query);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching tickets: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Management - Time Travel Museum</title>
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
                <h1 class="text-2xl font-bold">Tickets Management</h1>
                <a href="tickets-form.php" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Add New Ticket
                </a>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($ticket['type_billet']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo number_format($ticket['tarif'], 2); ?> €</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($ticket['reduction']); ?>%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($ticket['evenement_titre'] ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($ticket['visiteur_nom'] ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="tickets-form.php?edit=<?php echo $ticket['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="tickets-form.php" method="POST" class="inline">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>