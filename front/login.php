<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Travel Museum - Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <h2>Welcome Back</h2>
                <p>Access your Time Travel Museum account</p>
                
                <form action="login-process.php" method="POST">
                    <div class="form-group">
                        <label for="text">Email or ID</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="text" id="text" name="text"  required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="forgot-password.html" class="forgot-password">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="login-button">Login</button>
                    
                    <div class="signup-link">
                        Don't have an account? <a href="register.php">Sign up</a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="login-footer">
        <div class="copyright">
            <p>&#169; Time Travel Museum All Rights Reserved 2025</p>
        </div>
    </footer>
    <style>/* Login Page Styles */
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .remember-me {
            display: inline-flex;  /* Changed from flex to inline-flex */
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
             
        }
        
        .forgot-password {
            color: #000;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: #666;
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
        .terms-checkbox {
            display: flex;
            align-items: center;  /* Changed from flex-start to center */
            margin: 1.5rem 0;
            gap: 0.8rem;  /* Added gap for consistent spacing */
        }
        
        .terms-checkbox input {
            margin: 0;  /* Removed manual margins */
            flex-shrink: 0;  /* Prevents checkbox from shrinking */
            width: 1rem;  /* Fixed width for consistency */
            height: 1rem;  /* Fixed height for consistency */
        }
        
        .terms-text {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;  /* Better text alignment */
            margin: 0;  /* Remove any default margins */
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
        }</style>
</body>
</html>