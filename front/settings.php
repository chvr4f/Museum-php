<?php
session_start();
require 'config.php';

// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
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
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-5">
      <h1 class="text-2xl font-bold mb-6">Museum Settings</h1>

      <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm">
          <div class="p-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">General Information</h2>
          </div>
          <div class="p-4 space-y-6">
            <form action="#" method="POST">
              <div>
                <label class="block text-sm font-medium text-gray-700">Museum Name</label>
                <input type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Enter museum name">
              </div>

              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                <input type="email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="contact@museum.com">
              </div>

              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="+1 (555) 000-0000">
              </div>
              <hr class="my-6">

              <div>
                <h3 class="text-base font-medium text-gray-900">Opening Hours</h3>
                <div class="mt-4 space-y-4">
                  <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day): ?>
                    <div class="flex items-center space-x-4">
                      <div class="w-24">
                        <span class="text-sm text-gray-700"><?= $day ?></span>
                      </div>
                      <input type="time"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                      <span class="text-sm text-gray-500">to</span>
                      <input type="time"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <hr class="my-6">

              <div>
                <h3 class="text-base font-medium text-gray-900">Ticket Pricing</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Adult Ticket</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="text"
                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="0.00">
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Child Ticket</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="text"
                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="0.00">
                    </div>
                  </div>
                </div>
              </div>

              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 mt-6">
                <button type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>