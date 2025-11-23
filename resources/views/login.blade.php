<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
     <link rel="stylesheet" type="text/css" href="https://d1jougtdqdwy1v.cloudfront.net/css/5.2.3/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/5.2.3/bootstrap.bundle.min.js"></script>
</head>
<body>
       <section class="container-fluid login-container p-0">
            <!-- Left Panel -->
            <div class="col-lg-6 left-panel">
               

                <div class="welcome-content">
                    <h1 class="welcome-title">Hello,</h1>
                    <h2 class="welcome-subtitle">Welcome to CTA Management System!</h2>
                    <p class="welcome-description">
                        Your one-stop platform to create, monitor, and improve CTAs.
                    </p>
                    <img src="images/Vector.png" alt="">
                </div>
            </div>

            <!-- Right Panel -->
            <div class="col-lg-6 right-panel">
                <div class="login-form-container">
                    <h2 class="login-title">Login to your account</h2>
                    <p class="login-subtitle">Enter your credentials to access your dashboard</p>

                    <form id="loginForm" action="{{ route('login.post')}}" method="POST">
                         @csrf
                        <!-- Email/Username Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email/User Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your username or email" required>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                       

                        <!-- Login Button -->
                        <button type="submit" class="btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            LOGIN
                        </button>
                    </form>
                </div>
            </div>

       </section>
</body>
</html>


<script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
</script>