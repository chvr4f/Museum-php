<?php
// This MUST be at the VERY TOP of your file, before ANY HTML

// Check if visitor is logged in
$isVisitor = isset($_SESSION['role']) && $_SESSION['role'] === 'visiteur';
$visitorName = $_SESSION['prenom'] ?? 'Visitor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <title>Document</title>
</head>
<body>
<div class="first-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="logo"><a href="main.php">Time Travel</a></div>
          
            <div class="buttons">
                <button><a href="boutique.html" class="header-button">Online Boutique</a></button>
                <button><a href="tickets.php" class="header-button">Tickets</a></button>
                <?php if($isVisitor): ?>
                <div class="visitor-container">
                    <div class="visitor-name">
                        <i class='bx bx-user'></i>
                        <span><?php echo htmlspecialchars($visitorName); ?></span>
                    </div>
                    <a href="logout.php" class="logout-button">
                        <i class='bx bx-log-out'></i>
                    </a>
                </div>
                <?php else: ?>
                    <button><a href="login.php" class="header-button">Login</a></button>
                <?php endif; ?>
            </div>
        </div>

        <div class="second-header"> 
            <nav>
                <ul>
                    <li><a href="main.php">Home</a></li>
                    <li><a href="events.php">Exibitions & Events</a></li>
                    <li><a href="#visit">Visit</a></li>
                    <li><a href="collections.php">Collections</a></li>
                    <li><a href="#Contacts">Contact</a></li>
                </ul>
            </nav>
        </div>
        <style>
        /* Add this style for the visitor name display */
        .visitor-name {
            display: flex;
            align-items: center;
            gap: 8px;
            background: black;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .visitor-name i {
            font-size: 18px;
        }
        .visitor-container {
    display: flex;
    align-items: center;
    gap: 10px;
}



.visitor-name i {
    font-size: 18px;
}

.logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: black;
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
    background: #ddd;
    color: #000;
}



.logout-button i {
    font-size: 20px;
}
    </style>
</body>
</html>
