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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Open Sans', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        
        .timeline-marker {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline-marker:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #b38b59, #3a7ca5, #59a7b3);
            border-radius: 2px;
        }
    </style>
</head>

<body class="bg-gray-200 font-body">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-6 lg:p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-heading font-bold text-history-800">Museum Dashboard</h1>
                    <p class="text-antique-800"><?php echo date('l, F j, Y'); ?></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="p-2 rounded-full bg-white shadow-sm text-history-500 hover:bg-history-100">
                            <i class="fas fa-bell"></i>
                        </button>
                        <span class="absolute top-0 right-0 w-3 h-3 bg-danger-500 rounded-full"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <span class="font-medium text-history-800 hidden md:inline"><?php echo $_SESSION['username']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
                <?php
                $cards = [
                    [
                        'title' => 'Total Visitors', 
                        'value' => '12,486', 
                        'icon' => '<i class="fas fa-users"></i>',
                        'change' => ['value' => '12%', 'positive' => true], 
                        'color' => 'history'
                    ],
                    [
                        'title' => 'Ticket Revenue', 
                        'value' => '$24,350', 
                        'icon' => '<i class="fas fa-ticket-alt"></i>',
                        'change' => ['value' => '8.5%', 'positive' => true], 
                        'color' => 'primary'
                    ],
                    [
                        'title' => 'Exhibits', 
                        'value' => '28', 
                        'icon' => '<i class="fas fa-landmark"></i>',
                        'change' => ['value' => '3 new', 'positive' => true], 
                        'color' => 'antique'
                    ],
                    [
                        'title' => 'Gift Shop Sales', 
                        'value' => '$8,294', 
                        'icon' => '<i class="fas fa-gift"></i>',
                        'change' => ['value' => '5.2%', 'positive' => true], 
                        'color' => 'future'
                    ],
                ];
                foreach ($cards as $card):
                ?>
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-<?php echo $card['color']; ?>-500">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1"><?php echo $card['title']; ?></h3>
                                <p class="text-2xl font-semibold text-gray-800 mb-2"><?php echo $card['value']; ?></p>
                                <p class="text-sm <?php echo $card['change']['positive'] ? 'text-success-500' : 'text-danger-500'; ?>">
                                    <i class="fas fa-arrow-<?php echo $card['change']['positive'] ? 'up' : 'down'; ?> mr-1"></i>
                                    <?php echo $card['change']['value']; ?> from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-lg bg-<?php echo $card['color']; ?>-100 text-<?php echo $card['color']; ?>-600">
                                <?php echo $card['icon']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Charts Row -->
            <div class="grid gap-6 lg:grid-cols-3 mb-8">
                <!-- Visitor Traffic -->
                <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-heading font-semibold text-history-800">Visitor Traffic</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-xs bg-primary-100 text-primary-600 rounded-full">Weekly</button>
                            <button class="px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Monthly</button>
                            <button class="px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Yearly</button>
                        </div>
                    </div>
                    <div class="h-64">
                        <!-- Chart placeholder - would be replaced with Chart.js in production -->
                        <div class="flex h-full items-end space-x-2">
                            <?php 
                            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            foreach ($days as $i => $day): 
                                $height = 30 + rand(20, 70);
                                $peak = $i === 4; // Friday is peak day
                            ?>
                                <div class="flex-1 flex flex-col items-center">
                                    <div 
                                        class="w-full rounded-t-lg transition-all duration-300 hover:opacity-80 <?php echo $peak ? 'bg-primary-600' : 'bg-primary-400'; ?>" 
                                        style="height:<?php echo $height; ?>%"
                                    ></div>
                                    <span class="text-xs text-gray-500 mt-2"><?php echo $day; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Revenue Breakdown -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-heading font-semibold text-history-800 mb-6">Revenue Sources</h3>
                    <div class="flex flex-col items-center">
                        <!-- Donut Chart -->
                        <div class="relative w-40 h-40 mb-6">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    stroke="#e0e0e0" 
                                    stroke-width="2" 
                                    fill="none"
                                />
                                <path class="circle-1" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    stroke="#1a56a7" 
                                    stroke-width="2" 
                                    stroke-dasharray="60, 100" 
                                    fill="none"
                                />
                                <path class="circle-2" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    stroke="#b38b59" 
                                    stroke-width="2" 
                                    stroke-dasharray="25, 100" 
                                    stroke-dashoffset="-60" 
                                    fill="none"
                                />
                                <path class="circle-3" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    stroke="#59a7b3" 
                                    stroke-width="2" 
                                    stroke-dasharray="15, 100" 
                                    stroke-dashoffset="-85" 
                                    fill="none"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                <span class="text-2xl font-bold text-gray-800">$32,644</span>
                                <span class="text-xs text-gray-500">Total Revenue</span>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="w-full space-y-3">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 bg-[#1a56a7] rounded-full mr-2"></span>
                                    <span class="text-sm">Tickets</span>
                                </div>
                                <span class="text-sm font-medium">$19,586</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 bg-[#b38b59] rounded-full mr-2"></span>
                                    <span class="text-sm">Memberships</span>
                                </div>
                                <span class="text-sm font-medium">$8,167</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 bg-[#59a7b3] rounded-full mr-2"></span>
                                    <span class="text-sm">Gift Shop</span>
                                </div>
                                <span class="text-sm font-medium">$4,891</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Upcoming Events -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-heading font-semibold text-history-800">Upcoming Events</h3>
                    </div>
                    <div class="divide-y divide-gray-100 timeline-marker">
                        <?php
                        $events = [
                            [
                                'title' => 'Medieval Artifacts Exhibition', 
                                'date' => 'May 15, 2025', 
                                'time' => '10:00 AM',
                                'attendees' => 86,
                                'icon' => '<i class="fas fa-chess-rook"></i>',
                                'color' => 'antique'
                            ],
                            [
                                'title' => 'Space Exploration Workshop', 
                                'date' => 'May 18, 2025', 
                                'time' => '2:00 PM',
                                'attendees' => 42,
                                'icon' => '<i class="fas fa-rocket"></i>',
                                'color' => 'future'
                            ],
                            [
                                'title' => 'Ancient Egypt Tour', 
                                'date' => 'May 22, 2025', 
                                'time' => '11:00 AM',
                                'attendees' => 120,
                                'icon' => '<i class="fas fa-scroll"></i>',
                                'color' => 'history'
                            ],
                        ];
                        foreach ($events as $event):
                        ?>
                            <div class="p-6 transition-colors hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="p-3 rounded-lg bg-<?php echo $event['color']; ?>-100 text-<?php echo $event['color']; ?>-600 mr-4">
                                        <?php echo $event['icon']; ?>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?php echo $event['title']; ?></h4>
                                        <div class="flex flex-wrap items-center mt-2 gap-2">
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full flex items-center">
                                                <i class="far fa-calendar-alt mr-1"></i> <?php echo $event['date']; ?>
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full flex items-center">
                                                <i class="far fa-clock mr-1"></i> <?php echo $event['time']; ?>
                                            </span>
                                            <span class="text-xs bg-<?php echo $event['color']; ?>-100 text-<?php echo $event['color']; ?>-600 px-2 py-1 rounded-full">
                                                <?php echo $event['attendees']; ?> attendees
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <button class="w-full py-2 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center justify-center">
                            View All Events <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-heading font-semibold text-history-800">Visitor Feedback</h3>
                        <div class="flex items-center bg-primary-100 text-primary-600 px-3 py-1 rounded-full">
                            <i class="fas fa-star mr-1"></i>
                            <span class="font-medium">4.8</span>
                            <span class="ml-1 text-xs">average</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php
                        $reviews = [
                            [
                                'name' => 'Emma Wilson', 
                                'rating' => 5, 
                                'comment' => 'The medieval exhibition was breathtaking! The artifacts were incredibly well-preserved.', 
                                'time' => '2 hours ago', 
                                'exhibit' => 'Medieval History',
                                'avatar' => 'EW'
                            ],
                            [
                                'name' => 'Michael Chen', 
                                'rating' => 4, 
                                'comment' => 'Great experience overall. The space exhibit was particularly impressive with its interactive displays.', 
                                'time' => '5 hours ago', 
                                'exhibit' => 'Space Exploration',
                                'avatar' => 'MC'
                            ],
                            [
                                'name' => 'Sophia Rodriguez', 
                                'rating' => 5, 
                                'comment' => 'My children loved the ancient Egypt tour. The guide was very knowledgeable and engaging!', 
                                'time' => '1 day ago', 
                                'exhibit' => 'Ancient Egypt',
                                'avatar' => 'SR'
                            ],
                        ];
                        foreach ($reviews as $review):
                        ?>
                            <div class="p-6 transition-colors hover:bg-gray-50">
                                <div class="flex justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-history-100 text-history-600 flex items-center justify-center font-bold">
                                            <?php echo $review['avatar']; ?>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-800"><?php echo $review['name']; ?></h4>
                                            <div class="flex items-center mt-1">
                                                <div class="flex mr-2">
                                                    <?php for ($r = 0; $r < 5; $r++): ?>
                                                        <i class="fas fa-star text-<?php echo $r < $review['rating'] ? 'amber-400' : 'gray-300'; ?> text-xs"></i>
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
                                <p class="mt-3 text-sm text-gray-600 italic">"<?php echo $review['comment']; ?>"</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <button class="w-full py-2 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center justify-center">
                            View All Reviews <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Popular Exhibits -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-heading font-semibold text-history-800">Popular Exhibits</h3>
                        <p class="text-sm text-gray-500 mt-1">Ranked by visitor engagement</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php
                        $exhibits = [
                            [
                                'name' => 'Medieval History', 
                                'category' => 'European', 
                                'visitors' => '3,245', 
                                'engagement' => 92,
                                'trend' => 'up'
                            ],
                            [
                                'name' => 'Space Exploration', 
                                'category' => 'Modern', 
                                'visitors' => '2,876', 
                                'engagement' => 88,
                                'trend' => 'up'
                            ],
                            [
                                'name' => 'Ancient Egypt', 
                                'category' => 'Archaeology', 
                                'visitors' => '2,432', 
                                'engagement' => 85,
                                'trend' => 'steady'
                            ],
                            [
                                'name' => 'Dinosaur Fossils', 
                                'category' => 'Paleontology', 
                                'visitors' => '2,145', 
                                'engagement' => 82,
                                'trend' => 'down'
                            ],
                        ];

                        foreach ($exhibits as $exhibit):
                        ?>
                            <div class="p-4 transition-colors hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-800"><?php echo $exhibit['name']; ?></h4>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                        <?php echo $exhibit['category']; ?>
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-600 mr-2"><?php echo $exhibit['visitors']; ?> visitors</span>
                                        <div class="flex items-center text-xs <?php 
                                            echo $exhibit['trend'] === 'up' ? 'text-success-500' : 
                                            ($exhibit['trend'] === 'down' ? 'text-danger-500' : 'text-warning-500'); 
                                        ?>">
                                            <i class="fas fa-arrow-<?php 
                                                echo $exhibit['trend'] === 'up' ? 'up' : 
                                                ($exhibit['trend'] === 'down' ? 'down' : 'right'); 
                                            ?> mr-1"></i>
                                            <?php 
                                                echo $exhibit['trend'] === 'up' ? 'Growing' : 
                                                ($exhibit['trend'] === 'down' ? 'Declining' : 'Steady'); 
                                            ?>
                                        </div>
                                    </div>
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="bg-primary-600 h-2 rounded-full" 
                                            style="width: <?php echo $exhibit['engagement']; ?>%"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <button class="w-full py-2 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors flex items-center justify-center">
                            View All Exhibits <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>