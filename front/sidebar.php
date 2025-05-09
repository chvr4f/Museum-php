<?php
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$role = $_SESSION['role'];


?>

<div class="w-64 bg-dark-900 text-white flex flex-col">
  <div class="p-4 border-b border-dark-800">
    <h2 class="text-xl font-bold">Time Travel Museum</h2>
   
  </div>
  <nav class="flex-1 p-4 space-y-2">
    <!-- Dashboard -->
    <?php if ($role === 'admin'): ?>
      <a href="admin-dashboard.php" class="flex items-center space-x-3 p-2 rounded-lg bg-dark-800 text-white">
        <i class="fas fa-home w-5 text-center"></i>
        <span>Dashboard</span>
      </a>
    <?php endif; ?>

<!-- Employees -->
<?php if ($role === 'admin'): ?>
      <a href="employee-list.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-users-cog w-5 text-center"></i>
        <span>Employees</span>
      </a>
    <?php endif; ?>

    <!-- Collections -->
    <?php if ($role === 'admin' || $role === 'oeuvres'): ?>
      <a href="collection-list.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-box-open w-5 text-center"></i>
        <span>Collections</span>
      </a>
    <?php endif; ?>


    <!-- articles -->

    <?php if ($role === 'admin'): ?>
      <a href="article-list.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-box-open w-5 text-center"></i>
        <span>articles</span>
      </a>
    <?php endif; ?>
    <!-- Events -->
    <?php if ($role === 'admin' || $role === 'evenements'): ?>
      <a href="events-list.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-calendar-alt w-5 text-center"></i>
        <span>Events</span>
      </a>
    <?php endif; ?>

      <!-- visiteurs -->
    <?php if ($role === 'admin' || $role === 'visiteurs'): ?>
      <a href="visitors-list.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-box-open w-5 text-center"></i>
        <span>visitors</span>
      </a>
    <?php endif; ?>

    <!-- Reviews -->
    <?php if ($role === 'admin'): ?>

      <a href="reviews.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-star w-5 text-center"></i>
        <span>Reviews</span>
      </a>
    <?php endif; ?>

    <!-- Orders -->
    <?php if ($role === 'admin'): ?>

      <a href="orders.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-shopping-cart w-5 text-center"></i>
        <span>Orders</span>
      </a>
    <?php endif; ?>

    <!-- Settings -->
    <?php if ($role === 'admin'): ?>

      <a href="settings.php" class="flex items-center space-x-3 p-2 rounded-lg text-gray-300 hover:bg-dark-800 hover:text-white">
        <i class="fas fa-cog w-5 text-center"></i>
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