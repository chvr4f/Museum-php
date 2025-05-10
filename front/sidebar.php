<?php
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="w-64 bg-dark-900 text-white flex flex-col">
  <div class="p-4 border-b border-dark-800">
    <h2 class="text-xl font-bold">Time Travel Museum</h2>
  </div>
  <nav class="flex-1 p-4 space-y-2">
    <!-- Dashboard -->
    <?php if ($role === 'admin'): ?>
      <a href="admin-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'admin-dashboard.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
      </a>
    <?php endif; ?>

    <!-- Employees -->
    <?php if ($role === 'admin'): ?>
      <a href="employee-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'employee-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-users w-5 text-center"></i>
        <span>Employees</span>
      </a>
    <?php endif; ?>

    <!-- Collections -->
    <?php if ($role === 'admin' || $role === 'oeuvres'): ?>
      <a href="collection-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'collection-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-palette w-5 text-center"></i>
        <span>Collections</span>
      </a>
    <?php endif; ?>

    <!-- Articles -->
    <?php if ($role === 'admin'): ?>
      <a href="article-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'article-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-store w-5 text-center"></i>
        <span>Articles</span>
      </a>
    <?php endif; ?>

    <!-- Events -->
    <?php if ($role === 'admin' || $role === 'evenements'): ?>
      <a href="events-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'events-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-calendar-check w-5 text-center"></i>
        <span>Events</span>
      </a>
    <?php endif; ?>

    <!-- Tickets -->
    <?php if ($role === 'admin'): ?>
      <a href="tickets-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'tickets-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-ticket-alt w-5 text-center"></i>
        <span>Tickets</span>
      </a>
    <?php endif; ?>

    <!-- Visitors -->
    <?php if ($role === 'admin' || $role === 'visiteurs'): ?>
      <a href="visitors-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'visitors-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-user-friends w-5 text-center"></i>
        <span>Visitors</span>
      </a>
    <?php endif; ?>

    <!-- Reviews -->
     <?php if ($role === 'admin' || $role === 'visiteurs'): ?>
      <a href="reviews-list.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'reviews-list.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-star-half-alt w-5 text-center"></i>
        <span>Reviews</span>
      </a>
    <?php endif; ?>

    <!-- Orders -->
    <?php if ($role === 'admin'): ?>
      <a href="orders.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'orders.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-shopping-bag w-5 text-center"></i>
        <span>Orders</span>
      </a>
    <?php endif; ?>

    <!-- Settings -->
    <?php if ($role === 'admin'): ?>
      <a href="settings.php" class="flex items-center space-x-3 p-2 rounded-lg <?php echo ($current_page === 'settings.php') ? 'bg-primary-700 text-white' : 'text-gray-300 hover:bg-dark-800 hover:text-white'; ?>">
        <i class="fas fa-cogs w-5 text-center"></i>
        <span>Settings</span>
      </a>
    <?php endif; ?>

    <!-- Logout -->
    <a href="logout.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
      <i class="fas fa-sign-out-alt w-5 text-center"></i>
      <span>Logout</span>
    </a>
  </nav>
</div>