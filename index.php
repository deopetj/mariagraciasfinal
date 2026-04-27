<?php
session_start();
include 'conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maria Gracias - Korean BBQ with Filipino Twist</title>
    <meta name="keywords" content="Maria Gracias, Samgyupsal, Korean BBQ, Iloilo, Unlimited Samgyupsal">
    <meta name="description" content="Affordable Korean BBQ restaurant in Iloilo with unlimited samgyupsal and Filipino twist.">
    <meta name="author" content="Maria Gracias">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/homeindex.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" alt="Maria Gracias Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#branches">Branches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<center>    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">"Korean by heart,<br>Filipino in style."</h1>
                <p class="hero-subtitle">Affordable Korean BBQ restaurant in Iloilo with unlimited samgyupsal and Filipino twist.</p>
                <button class="btn btn-primary btn-lg pulse" id="reserveBtn">Reserve Now</button>
            </div>
        </div>
    </section>
      </center>
    <!-- Menu Section -->
    <section id="menu" class="menu-section">
        <div class="container">
            <h2 class="section-title">Our Menu</h2>
            <div class="row">
                <div class="col-12">
                    <div class="menu-image">
                        <img src="images/blog-1.jpg" alt="Maria Gracias Menu" class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Branches Section -->
    <section id="branches" class="branches-section">
        <div class="container">
            <h2 class="section-title">Our Branches</h2>
            <div class="row">
                <!-- Branch 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/delcarmen.jpg" alt="Del Carmen Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Del Carmen</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> 14th Del Carmen St., Jaro, Iloilo City, Philippines beside Jo's Chiken Inato
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/lapaz.jpg" alt="Lapaz Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Lapaz</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> Romnelcar Bldg., Corner Gustilo Burgos St., Lapaz, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/mandu.jpg" alt="Mandurriao Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Mandurriao</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> 2/F Redsquare Building, Smallville Complex, Mandurriao, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Branch 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="branch-card">
                        <div class="branch-image">
                            <img src="branches/lapuz.jpg" alt="Lapuz Branch">
                        </div>
                        <div class="branch-content">
                            <h3 class="branch-name">Lapuz</h3>
                            <p class="branch-location">
                                <i class="fas fa-map-marker-alt"></i> Ground Floor, Sea Eagle Boulevard, Brgy. Libertad Lapuz, Iloilo City, Philippines
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h3 class="mb-4">Maria Gracias Samgyupsal</h3>
                        <p class="mb-4">Maria Gracias Samgyupsal is more than just a restaurant—it's a celebration of flavors, community, and shared moments. Nestled in the vibrant city of Iloilo, Philippines, we bring the joy of Korean BBQ to your table with a unique Filipino twist.</p>
                        <p class="mb-4">Our story began with a simple idea: to create a space where everyone could enjoy unlimited samgyupsal without worrying about the cost. Over time, we've grown into a beloved destination, known for our affordable prices, warm hospitality, and delicious food.</p>
                        <p>At Maria Gracias Samgyupsal, we specialize in unlimited samgyupsal, offering premium cuts of pork belly that you can grill to perfection. Our menu is a fusion of Korean and Filipino flavors, featuring a variety of side dishes and sauces that reflect our local culture.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="images/about-img.jpg" alt="About Maria Gracias" class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4>Maria Gracias Samgyupsal</h4>
                    <p>Korean by heart, Filipino in style.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> <a href="tel:09103857426">09103857426</a></p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:mgsamgyupsal@gmail.com">mgsamgyupsal@gmail.com</a></p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Maria Gracias Samgyupsal. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a.nav-link').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId.startsWith('#')) {
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.padding = '5px 0';
                document.querySelector('.navbar-brand img').style.width = '60px';
                document.querySelector('.navbar-brand img').style.height = '60px';
            } else {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.padding = '10px 0';
                document.querySelector('.navbar-brand img').style.width = '80px';
                document.querySelector('.navbar-brand img').style.height = '80px';
            }
        });

        // Handle login/register functionality
        function handleLogin() {
            Swal.fire({
                title: 'Login',
                html: `
                    <input type="email" id="email" class="swal2-input" placeholder="Email">
                    <input type="password" id="password" class="swal2-input" placeholder="Password">
                    <br>
                     <br>
                    <h6>Don't have an account? Click Sign Up below.</h6>
                `,
                confirmButtonText: 'Login',
                showCancelButton: true,
                cancelButtonText: 'Sign Up',
                preConfirm: () => {
                    const email = document.getElementById('email').value.trim();
                    const password = document.getElementById('password').value.trim();

                    if (!email || !password) {
                        Swal.showValidationMessage('Please enter both email and password');
                        return false;
                    }

                    return fetch('login.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email, password })
                    })
                    .then(response => response.text())
                    .then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (error) {
                            console.error("JSON Parsing Error:", error);
                            Swal.fire('Error', 'Invalid server response', 'error');
                            throw error;
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Login successful', 'success').then(() => {
                                if (data.role === 'admin') {
                                    window.location.href = "admin1/index.php";
                                } else if (data.role === 'user') {
                                    window.location.href = "home.php";
                                } else {
                                    Swal.fire('Error', 'Unknown role', 'error');
                                }
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                        Swal.fire('Error', 'Server error: Check console for details', 'error');
                    });
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    handleRegister();
                }
            });
        }

        function handleRegister() {
            Swal.fire({
                title: 'Sign Up',
                html: `
                    <input type="number" id="cnum" class="swal2-input" placeholder="Contact Number">
                    <input type="email" id="email" class="swal2-input" placeholder="Email">
                    <input type="password" id="password" class="swal2-input" placeholder="Password">
                    <style>
                        input[type="number"]::-webkit-inner-spin-button,
                        input[type="number"]::-webkit-outer-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }     
                    </style>
                `,
                confirmButtonText: 'Sign Up',
                preConfirm: () => {
                    const cnum = document.getElementById('cnum').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const password = document.getElementById('password').value.trim();

                    if (!cnum || !email || !password) {
                        Swal.showValidationMessage('Please fill in all the fields');
                        return false;
                    }

                    if (!/^(09\d{9})$/.test(cnum)) {
                        Swal.showValidationMessage('Contact number must start with 09 and be exactly 11 digits long.');
                        return false;
                    }

                    const formData = new FormData();
                    formData.append('cnum', cnum);
                    formData.append('email', email);
                    formData.append('password', password);

                    return fetch('register.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Registration successful', 'success').then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.fire('Error', 'Server error: Check console for details', 'error');
                    });
                }
            });
        }

        // Add event listener to the reserve button
        document.getElementById('reserveBtn').addEventListener('click', function(event) {
            event.preventDefault();
            handleLogin();
        });
    </script>
</body>
</html>