<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum - Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <style>
        /* Checkout Page Styles */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            line-height: 1.6;
        }
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: 'Montserrat', sans-serif;
    color: #333;
    padding-top: 140px; /* This accounts for both headers (85px + 55px) */
}

/* Header Styles */
header {
    background-color: gray;
    position: fixed;
    width: 100%;
    z-index: 1000;
    transition: transform 0.3s ease-in-out;
    top: 0;
}

.first-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #000;
    color: white;
    padding: 1rem 2rem;
    position: relative;
    height: 85px;
}

.second-header {
    background-color: #000;
    padding: 1rem 0;
    height: 55px;
}
        .checkout-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .checkout-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkout-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #000;
        }

        .checkout-header p {
            color: #666;
        }

        .checkout-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .checkout-summary {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            align-self: start;
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-control:focus {
            border-color: #000;
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .payment-methods {
            margin-top: 2rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #000;
        }

        .payment-method.active {
            border-color: #000;
            background-color: #f8f8f8;
        }

        .payment-method input {
            margin-right: 1rem;
        }

        .payment-method-icon {
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }

        .btn:hover {
            background: #333;
        }

        .order-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .order-summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .cart-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 1.5rem;
        }

        .cart-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 1rem;
            border-radius: 4px;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-title {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .cart-item-price {
            color: #666;
            font-size: 0.9rem;
        }

        .cart-item-quantity {
            color: #666;
            font-size: 0.9rem;
        }

        .secure-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1rem;
            color: #4CAF50;
            font-size: 0.9rem;
        }

        .secure-checkout i {
            margin-right: 0.5rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .checkout-header h1 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
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
            <div class="logo"><a href="main.html">Time Travel</a></div>
          
            <div class="buttons">
                <button><a href="boutique.html" class="header-button">Online Boutique</a></button>
                <button><a href="tickets.html" class="header-button">Tickets</a></button>
                
                <button><a href="login.html" class="header-button">Login</a></button>

                <div class="cart-icon" id="cartIcon">
                    <i class='bx bx-cart'></i>
                    <span class="cart-count">0</span>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="checkout-container">
            <div class="checkout-header">
                <h1>Checkout</h1>
                <p>Complete your purchase and step through time with your treasures</p>
            </div>

            <div class="checkout-form">
                <h2 class="section-title">Shipping Information</h2>
                <form id="checkoutForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <input type="text" id="address" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="zip">ZIP Code</label>
                            <input type="text" id="zip" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" class="form-control" required>
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                        </select>
                    </div>

                    <h2 class="section-title">Payment Method</h2>
                    <div class="payment-methods">
                        <div class="payment-method active">
                            <input type="radio" id="creditCard" name="paymentMethod" value="creditCard" checked>
                            <i class='bx bx-credit-card payment-method-icon'></i>
                            <label for="creditCard">Credit Card</label>
                        </div>

                        <div class="payment-method">
                            <input type="radio" id="paypal" name="paymentMethod" value="paypal">
                            <i class='bx bxl-paypal payment-method-icon'></i>
                            <label for="paypal">PayPal</label>
                        </div>

                        <div class="payment-method">
                            <input type="radio" id="applePay" name="paymentMethod" value="applePay">
                            <i class='bx bxl-apple payment-method-icon'></i>
                            <label for="applePay">Apple Pay</label>
                        </div>
                    </div>

                    <div id="creditCardForm">
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" id="cardNumber" class="form-control" placeholder="1234 5678 9012 3456">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiryDate">Expiry Date</label>
                                <input type="text" id="expiryDate" class="form-control" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" class="form-control" placeholder="123">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cardName">Name on Card</label>
                            <input type="text" id="cardName" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" id="saveInfo" checked>
                        <label for="saveInfo">Save this information for next time</label>
                    </div>

                    <button type="submit" class="btn">Complete Purchase</button>
                    <div class="secure-checkout">
                        <i class='bx bx-lock-alt'></i>
                        <span>Secure Checkout</span>
                    </div>
                </form>
            </div>

            <div class="checkout-summary">
                <h2 class="section-title">Order Summary</h2>
                
                <div class="cart-items" id="checkoutCartItems">
                    <!-- Cart items will be populated by JavaScript -->
                    <p>Loading your items...</p>
                </div>

                <div class="order-summary-item">
                    <span>Subtotal</span>
                    <span>$<span id="subtotal">0.00</span></span>
                </div>
                <div class="order-summary-item">
                    <span>Shipping</span>
                    <span>$<span id="shipping">5.99</span></span>
                </div>
                <div class="order-summary-item">
                    <span>Tax</span>
                    <span>$<span id="tax">0.00</span></span>
                </div>
                <div class="order-summary-total">
                    <span>Total</span>
                    <span>$<span id="total">0.00</span></span>
                </div>

                <div class="promo-code" style="margin-top: 1.5rem;">
                    <div class="form-group">
                        <label for="promoCode">Promo Code</label>
                        <div style="display: flex;">
                            <input type="text" id="promoCode" class="form-control" placeholder="Enter code" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button style="background: #ddd; border: 1px solid #ddd; border-left: none; border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0 1rem; cursor: pointer;">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Load cart from localStorage
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const checkoutCartItems = document.getElementById('checkoutCartItems');
            const subtotalElement = document.getElementById('subtotal');
            const shippingElement = document.getElementById('shipping');
            const taxElement = document.getElementById('tax');
            const totalElement = document.getElementById('total');
            
            // Update order summary
            function updateOrderSummary() {
                let subtotal = 0;
                checkoutCartItems.innerHTML = '';
                
                if (cart.length === 0) {
                    checkoutCartItems.innerHTML = '<p>Your cart is empty</p>';
                } else {
                    cart.forEach(item => {
                        subtotal += item.price * item.quantity;
                        
                        const cartItem = document.createElement('div');
                        cartItem.className = 'cart-item';
                        cartItem.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                            <div class="cart-item-details">
                                <div class="cart-item-title">${item.name}</div>
                                <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                                <div class="cart-item-quantity">Quantity: ${item.quantity}</div>
                            </div>
                        `;
                        
                        checkoutCartItems.appendChild(cartItem);
                    });
                }
                
                const shipping = 5.99; // Flat rate shipping
                const tax = subtotal * 0.08; // Example tax rate of 8%
                const total = subtotal + shipping + tax;
                
                subtotalElement.textContent = subtotal.toFixed(2);
                taxElement.textContent = tax.toFixed(2);
                totalElement.textContent = total.toFixed(2);
            }
            
            // Initialize order summary
            updateOrderSummary();
            
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    paymentMethods.forEach(m => m.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelector(`#${this.querySelector('input').id}`).checked = true;
                });
            });
            
            // Form submission
            const checkoutForm = document.getElementById('checkoutForm');
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (cart.length === 0) {
                    alert('Your cart is empty. Please add items before checking out.');
                    return;
                }
                
                // In a real application, you would process the payment here
                // For demo purposes, we'll just show a success message
                alert('Order placed successfully! Thank you for your purchase.');
                
                // Clear the cart
                localStorage.removeItem('cart');
                
                // Redirect to confirmation page (which you would create)
                window.location.href = 'confirmation.html';
            });
            
            // Cart count update
            const cartCount = document.querySelector('.cart-count');
            const updateCartCount = () => {
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = count;
            };
            updateCartCount();
        });
    </script>
</body>
</html>