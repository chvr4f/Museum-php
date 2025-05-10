<?php
session_start();
require 'config.php';

// Fetch articles from database
$articles = [];
try {
    $stmt = $pdo->query("SELECT * FROM article ORDER BY id DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching articles: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum - Online Boutique</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <style>
        /* Boutique-specific styles */
        .boutique-hero {
            position: relative;
            height: 500px;
            overflow: hidden;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
            url(pics/boutique.avif); 
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .boutique-hero-content {
            margin-top: 5%;
        }

        .boutique-hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .boutique-hero-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        .boutique-section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .boutique-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .boutique-header h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .boutique-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .boutique-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .boutique-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .boutique-item:hover {
            transform: translateY(-10px);
        }

        .boutique-item-img {
            height: 250px;
            overflow: hidden;
        }

        .boutique-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .boutique-item:hover .boutique-item-img img {
            transform: scale(1.05);
        }

        .boutique-item-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .boutique-item h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .boutique-item p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .boutique-item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 1rem;
        }

        .boutique-item-button {
            display: block;
            width: 100%;
            padding: 0.8rem;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
            text-decoration: none;
            margin-top: auto;
        }

        .boutique-item-button:hover {
            background: #333;
        }

        .boutique-categories {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .boutique-category {
            padding: 0.6rem 1.2rem;
            background: #f8f8f8;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .boutique-category.active,
        .boutique-category:hover {
            background: #000;
            color: white;
        }

        /* Cart styles */
        .cart-icon {
            position: relative;
            cursor: pointer;
            margin-top: 3%;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #d4af37;
            color: #000;
            border-radius: 50%;
            width: 15px;
            height: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .cart-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            width: 350px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-radius: 8px;
            padding: 1rem;
            z-index: 1000;
        }
        
        .cart-dropdown.active {
            display: block;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        
        .cart-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-title {
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        
        .cart-item-price {
            color: #666;
            font-size: 0.9rem;
        }
        
        .cart-total {
            font-weight: 700;
            margin-top: 1rem;
            text-align: right;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .cart-actions a {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
        }
        
        .view-cart {
            background: #f8f8f8;
            color: #000;
        }
        
        .checkout {
            background: #000;
            color: white;
        }

        /* Delete button styles */
        .remove-item {
            color: #ff4444;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s;
            background: none;
            border: none;
            padding: 0;
            margin-left: 0.5rem;
        }
        
        .remove-item:hover {
            color: #cc0000;
        }

        @media (max-width: 768px) {
            .boutique-hero {
                height: 400px;
            }
            
            .boutique-hero-content h1 {
                font-size: 2.5rem;
            }
            
            .boutique-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .cart-dropdown {
                width: 300px;
                right: -50px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="first-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
            <div class="logo"><a href="main.php">Time Travel</a></div>
          
            <div class="buttons">
                <button><a href="boutique.php" class="header-button">Online Boutique</a></button>
                <button><a href="tickets.php" class="header-button">Tickets</a></button>
                
                <button><a href="login.php" class="header-button">Login</a></button>

                <div class="cart-icon" id="cartIcon">
                    <i class='bx bx-cart'></i>
                    <span class="cart-count">0</span>
                    <div class="cart-dropdown" id="cartDropdown">
                        <div class="cart-items" id="cartItems">
                            <p class="empty-cart-message">Your cart is empty</p>
                        </div>
                        <div class="cart-total">
                            Total: $<span id="cartTotal">0.00</span>
                        </div>
                        <div class="cart-actions">
                            <a href="cart.php" class="view-cart">View Cart</a>
                            <a href="checkout.php" class="checkout">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Boutique Hero Section -->
        <section class="boutique-hero">
            <div class="boutique-hero-content">
                <h1>Museum Boutique</h1>
                <p>Bring a piece of history home with our exclusive collection of time-inspired merchandise</p>
            </div>
        </section>

        <!-- Boutique Categories -->
        <section class="boutique-section">
            <div class="boutique-categories">
                <div class="boutique-category active">All Items</div>
                <div class="boutique-category">Ancient Era</div>
                <div class="boutique-category">Medieval</div>
                <div class="boutique-category">Industrial Age</div>
                <div class="boutique-category">Future Tech</div>
                <div class="boutique-category">Books</div>
            </div>

            <div class="boutique-header">
                <h2>Featured Products</h2>
                <p>Discover unique gifts and souvenirs from across the ages</p>
            </div>

            <div class="boutique-grid">
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $article): ?>
                        <div class="boutique-item">
                            <div class="boutique-item-img">
                                <img src="<?php echo htmlspecialchars($article['image_article'] ?? 'pics/default-product.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($article['nom']); ?>">
                            </div>
                            <div class="boutique-item-content">
                                <h3><?php echo htmlspecialchars($article['nom']); ?></h3>
                                <p><?php echo htmlspecialchars($article['description']); ?></p>
                                <div class="boutique-item-price">$<?php echo number_format($article['prix'], 2); ?></div>
                                <button class="boutique-item-button add-to-cart" 
                                        data-id="<?php echo htmlspecialchars($article['id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($article['nom']); ?>" 
                                        data-price="<?php echo htmlspecialchars($article['prix']); ?>" 
                                        data-image="<?php echo htmlspecialchars($article['image_article'] ?? 'pics/default-product.jpg'); ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No products available in the boutique at this time.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <section class="footer">
        <div class="end">
            <div class="company-info">
                <h2>Time Travel</h2>
            </div>
            <div class="social">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-tiktok'></i></a>
            </div>
        </div>
        <div class="ends">
            <div class="support">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">Product</a></li>
                    <li><a href="#">Help & Support</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">Terms Of Use</a></li>
                </ul>
            </div>
            <div class="guides">
                <h3>View Guides</h3>
                <ul>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog Posts</a></li>
                    <li><a href="#">Our Branches</a></li>
                </ul>
            </div>
            <div class="contacts">
                <h3>Contacts</h3>
                <ul class="contact-list">
                    <li><i class='bx bxs-map'></i>88 Time Travel Avenue</li>
                    <li><i class='bx bxs-phone'></i>+1 555-TIME-TRV</li>
                    <li><i class='bx bxs-mail-send'></i>shop@timetravelmuseum.org</li>
                </ul>
            </div>
        </div>
    </section>
    
    <div class="copyright">
        <p>&#169; Time Travel All Rights Reserved 2023</p>
    </div>

    <script>
        // Shopping cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            const cartIcon = document.getElementById('cartIcon');
            const cartDropdown = document.getElementById('cartDropdown');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const cartCount = document.querySelector('.cart-count');
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Initialize cart display
            updateCart();
            
            // Toggle cart dropdown
            cartIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                cartDropdown.classList.toggle('active');
            });
            
            // Close cart when clicking outside
            document.addEventListener('click', function() {
                cartDropdown.classList.remove('active');
            });
            
            // Add to cart functionality
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));
                    const image = this.getAttribute('data-image');
                    
                    // Check if item already in cart
                    const existingItem = cart.find(item => item.id === id);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cart.push({
                            id,
                            name,
                            price,
                            image,
                            quantity: 1
                        });
                    }
                    
                    // Save to localStorage
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    updateCart();
                    cartDropdown.classList.add('active');
                    
                    // Add animation
                    const originalText = this.textContent;
                    this.textContent = 'Added!';
                    this.style.backgroundColor = '#4CAF50';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.backgroundColor = '#000';
                    }, 1000);
                });
            });
            
            // Update cart display
            function updateCart() {
                // Clear cart items
                cartItems.innerHTML = '';
                
                if (cart.length === 0) {
                    cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                } else {
                    let total = 0;
                    
                    cart.forEach(item => {
                        total += item.price * item.quantity;
                        
                        const cartItem = document.createElement('div');
                        cartItem.className = 'cart-item';
                        cartItem.innerHTML = `
                            <img src="${item.image}" alt="${item.name}">
                            <div class="cart-item-details">
                                <div class="cart-item-title">${item.name}</div>
                                <div class="cart-item-price">$${item.price.toFixed(2)} × ${item.quantity}</div>
                            </div>
                            <button class="remove-item" data-id="${item.id}" title="Remove item">
                                <i class='bx bx-trash'></i>
                            </button>
                        `;
                        
                        cartItems.appendChild(cartItem);
                    });
                    
                    // Add remove item functionality
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const id = this.getAttribute('data-id');
                            cart = cart.filter(item => item.id !== id);
                            localStorage.setItem('cart', JSON.stringify(cart));
                            updateCart();
                            
                            // Show confirmation message
                            const confirmation = document.createElement('div');
                            confirmation.textContent = 'Item removed from cart';
                            confirmation.style.position = 'fixed';
                            confirmation.style.bottom = '20px';
                            confirmation.style.right = '20px';
                            confirmation.style.backgroundColor = '#ff4444';
                            confirmation.style.color = 'white';
                            confirmation.style.padding = '10px 20px';
                            confirmation.style.borderRadius = '4px';
                            confirmation.style.zIndex = '1000';
                            document.body.appendChild(confirmation);
                            
                            setTimeout(() => {
                                document.body.removeChild(confirmation);
                            }, 2000);
                        });
                    });
                    
                    cartTotal.textContent = total.toFixed(2);
                }
                
                // Update cart count
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = count;
            }
            
            // Simple category filter functionality
            document.querySelectorAll('.boutique-category').forEach(category => {
                category.addEventListener('click', function() {
                    // Remove active class from all categories
                    document.querySelectorAll('.boutique-category').forEach(c => {
                        c.classList.remove('active');
                    });
                    
                    // Add active class to clicked category
                    this.classList.add('active');
                    
                    // In a real implementation, you would filter products here
                    // This is just a placeholder for the UI
                });
            });
        });
    </script>
</body>
</html>