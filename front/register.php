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
    /* Registration Page Styles */
    .login-main {
        min-height: calc(100vh - 160px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    .login-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1000px;
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-top: 4%;
    }
    
    .login-image {
        position: relative;
    }
    
    .login-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .login-form {
        margin-top: 10%;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .login-form h2 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #000;
    }
    
    .login-form p {
        color: #666;
        margin-bottom: 2rem;
    }
    
    .login-form .form-group {
        margin-bottom: 1.5rem;
    }
    
    .login-form label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }
    
    .login-form .input-with-icon {
        position: relative;
    }
    
    .login-form .input-with-icon i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }
    
    .login-form input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: 'Montserrat', sans-serif;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .login-form input:focus {
        outline: none;
        border-color: #000;
    }
    
    .login-button {
        width: 100%;
        padding: 1rem;
        background: #000;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        margin-bottom: 1.5rem;
    }
    
    .login-button:hover {
        background: #333;
    }
    
    .signup-link {
        text-align: center;
        color: #666;
        margin-top: 1.5rem;
        font-size: 0.9rem;
    }
    
    .signup-link a {
        color: #000;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-left: 0.3rem;
        position: relative;
    }
    
    .signup-link a:hover {
        color: #666;
    }
    
    /* Optional: Add underline animation on hover */
    .signup-link a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: #666;
        transition: width 0.3s ease;
    }
    
    .signup-link a:hover::after {
        width: 100%;
    }
    
    .login-footer {
        background: #000;
        color: white;
        text-align: center;
        padding: 1.5rem;
    }
    
    /* Registration-specific styles */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        border: 1px solid transparent;
    }
    
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
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
        font-size: 0.85rem;
        color: #666;
    }
    
    .password-requirements li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .password-requirements li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #000;
    }
    
    .terms-checkbox {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
        gap: 0.8rem;
    }
    
    .terms-checkbox input {
        margin: 0;
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
    }
    
    .terms-text {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.4;
    }
    
    .terms-text a {
        color: #000;
        font-weight: 500;
        text-decoration: none;
    }
    
    .terms-text a:hover {
        text-decoration: underline;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            grid-template-columns: 1fr;
        }
        
        .login-image {
            display: none;
        }
        
        .login-form {
            padding: 2rem;
        }
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
                            <input type="text" id="fullname" name="fullname"  required
                                   value="<?php echo isset($_SESSION['register_data']['fullname']) ? htmlspecialchars($_SESSION['register_data']['fullname']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email"  required
                                   value="<?php echo isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Create Password*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password"  required>
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
                            <input type="password" id="confirm-password" name="confirm-password"  required>
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


