<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum - Register</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional styles specific to registration page */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
        }
        
        .password-requirements {
            margin: 1rem 0;
            padding: 0;
            list-style: none;
        }
        
        .password-requirements li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: 0.85rem;
        }
        
        .password-requirements li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #d4af37;
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            margin: 1.5rem 0;
        }
        
        .terms-checkbox input {
            margin-right: 0.8rem;
            margin-top: 0.2rem;
        }
        
        .terms-text {
            font-size: 0.85rem;
            color: #666;
        }
        
        .terms-text a {
            color: #000;
            font-weight: 500;
            text-decoration: none;
        }
        
        .terms-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="first-header">
            <div class="logo"><a href="main.php">Time Travel</a></div>
        </div>
    </header>

    <main class="login-main">
        <section class="login-container">
            <div class="login-image">
                <img src="pics/login.avif"> 
            </div>
            <div class="login-form">
                <h2>Begin Your Journey</h2>
                <p>Create your Time Travel Museum account</p>
                
                <?php if (isset($_SESSION['register_errors'])): ?>
                    <div class="alert alert-error">
                        <?php foreach ($_SESSION['register_errors'] as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                        <?php unset($_SESSION['register_errors']); ?>
                    </div>
                <?php endif; ?>
                
                <form action="register-process.php" method="POST">
                    <div class="form-group">
                        <label for="fullname">Full Name*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="fullname" name="fullname" placeholder="Your full name" required
                                   value="<?php echo isset($_SESSION['register_data']['fullname']) ? htmlspecialchars($_SESSION['register_data']['fullname']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="your@email.com" required
                                   value="<?php echo isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Create Password*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="password-strength">Password strength: <span id="strength-indicator">Weak</span></div>
                        <ul class="password-requirements">
                            <li>Minimum 8 characters</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" name="terms" required
                            <?php echo isset($_SESSION['register_data']['terms']) ? 'checked' : ''; ?>>
                        <label for="terms" class="terms-text">
                            I agree to the <a href="terms.html">Terms of Service</a> and <a href="privacy.html">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="login-button">Create Account</button>
                    
                    <div class="signup-link">
                        Already have an account? <a href="login.php">Sign in</a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="login-footer">
        <div class="copyright">
            <p>&#169; Time Travel Museum All Rights Reserved 2023</p>
        </div>
    </footer>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('strength-indicator');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            // Contains number
            if (password.match(/\d/)) strength++;
            // Contains special character
            if (password.match(/[!@#$%^&*(),.?":{}|<>]/)) strength++;
            
            // Update indicator
            const strengthText = ['Weak', 'Medium', 'Strong'][strength - 1] || 'Weak';
            const strengthColors = ['#ff5252', '#ffb142', '#33d9b2'];
            
            strengthIndicator.textContent = strengthText;
            strengthIndicator.style.color = strengthColors[strength - 1] || '#ff5252';
        });
    </script>
</body>
</html>
<?php unset($_SESSION['register_data']); ?>