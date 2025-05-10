<?php
session_start();
require 'config.php';

// Verify admin role only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$success = $error = '';
$ticket = [
    'id' => '',
    'tarif' => '',
    'reduction' => 0,
    'type_billet' => 'regular' // Default to regular
];
$edit_mode = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete'])) {
            // Delete ticket
            $stmt = $pdo->prepare("DELETE FROM billets WHERE id = ?");
            $stmt->execute([$_POST['ticket_id']]);
            $_SESSION['success'] = "Ticket deleted successfully!";
            header('Location: tickets-list.php');
            exit();
        } elseif (isset($_POST['update'])) {
            // Update ticket
            $stmt = $pdo->prepare("UPDATE billets SET 
                tarif = ?, 
                reduction = ?, 
                type_billet = ?
                WHERE id = ?");
            $stmt->execute([
                $_POST['tarif'],
                $_POST['reduction'],
                $_POST['type_billet'],
                $_POST['ticket_id']
            ]);
            $_SESSION['success'] = "Ticket updated successfully!";
            header('Location: tickets-list.php');
            exit();
        } else {
            // Add new ticket
            $stmt = $pdo->prepare("INSERT INTO billets 
                (tarif, reduction, type_billet) 
                VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['tarif'],
                $_POST['reduction'],
                $_POST['type_billet']
            ]);
            $_SESSION['success'] = "Ticket added successfully!";
            header('Location: tickets-list.php');
            exit();
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM billets WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ticket) {
            throw new Exception("Ticket not found");
        }
        $edit_mode = true;
    } catch (PDOException $e) {
        $error = "Error fetching ticket: " . $e->getMessage();
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
                <form action="tickets-form.php" method="POST">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                        <input type="hidden" name="update" value="1">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="type_billet" class="block font-medium mb-1">Ticket Type*</label>
                        <select id="type_billet" name="type_billet" class="w-full p-2 border border-gray-300 rounded" required>
                            <option value="regular" <?php echo ($ticket['type_billet'] === 'regular') ? 'selected' : ''; ?>>Regular</option>
                            <option value="vip" <?php echo ($ticket['type_billet'] === 'vip') ? 'selected' : ''; ?>>VIP</option>
                            <option value="familial" <?php echo ($ticket['type_billet'] === 'familial') ? 'selected' : ''; ?>>Familial</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="tarif" class="block font-medium mb-1">Price (€)*</label>
                        <input type="number" id="tarif" name="tarif" step="0.01" min="0" 
                               value="<?php echo htmlspecialchars($ticket['tarif']); ?>" 
                               class="w-full p-2 border border-gray-300 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label for="reduction" class="block font-medium mb-1">Discount (%)</label>
                        <input type="number" id="reduction" name="reduction" min="0" max="100" 
                               value="<?php echo htmlspecialchars($ticket['reduction']); ?>" 
                               class="w-full p-2 border border-gray-300 rounded">
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
                        <?php echo $edit_mode ? 'Update Ticket' : 'Add Ticket'; ?>
                    </button>

                    <?php if ($edit_mode): ?>
                        <a href="tickets-list.php" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>