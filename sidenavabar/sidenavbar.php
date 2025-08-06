<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Responsive Side Navigation</title>
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap');

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #4a5568;
            --secondary-color: #2d3748;
            --accent-color: #4299e1;
            --text-color: #ffffff;
            --hover-color: #e6f2ff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7fafc;
            color: #2d3748;
            line-height: 1.6;
        }

        /* Side Navigation Styles */
        .side-navbar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-color);
            transition: all 0.3s ease;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        /* Navbar Header */
        .navbar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 15px;
            background-color: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 22px;
            font-weight: 600;
            color: var(--text-color);
        }

        .logo i {
            margin-right: 10px;
        }

        /* Toggle Button */
        .toggle-btn {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 22px;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 5px;
            border-radius: 5px;
        }

        .toggle-btn:hover {
            color: var(--accent-color);
            transform: rotate(90deg);
        }

        /* Navigation Links */
        .nav-links {
            margin-top: 20px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            color: var(--text-color);
            text-decoration: none;
            padding: 15px 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-links a:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .nav-links a i {
            margin-right: 15px;
            font-size: 20px;
            width: 30px;
            text-align: center;
            color: var(--accent-color);
            transition: transform 0.3s ease;
        }

        .nav-links a:hover i {
            transform: scale(1.1);
        }

        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid var(--accent-color);
        }

        .profile-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .profile-details p {
            margin: 0;
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }

        /* Mobile Responsiveness */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        @media screen and (max-width: 768px) {
            .side-navbar {
                width: 0;
                transform: translateX(-100%);
                opacity: 0;
            }

            .mobile-toggle {
                display: block;
            }

            .side-navbar.active {
                width: 100%;
                transform: translateX(0);
                opacity: 1;
            }

            .nav-links a {
                padding: 15px 15px;
            }
        }

        /* Collapsed State */
        .side-navbar.collapsed {
            width: 80px;
        }

        .side-navbar.collapsed .nav-text {
            display: none;
        }

        .side-navbar.collapsed .nav-links a i {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <!-- Side Navbar -->
    <nav class="side-navbar">
        <div class="navbar-header">
            <div class="logo">
                <i class="fas fa-bolt"></i>
                <span>Dashboard</span>
            </div>
            <button class="toggle-btn" aria-label="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="nav-links">
            <div class="profile-section">
                <img src="/api/placeholder/50/50" alt="Profile" class="profile-img">
                <div class="profile-details">
                    <h4>John Doe</h4>
                    <p>Administrator</p>
                </div>
            </div>

            <a href="../../twinkleadmin/dashboard.php">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/slider.php">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="nav-text">Upload Slider</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/uploadreel.php">
                <i class="fas fa-video"></i>
                <span class="nav-text">Upload Reels</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/uploadstaff.php">
                <i class="fas fa-users"></i>
                <span class="nav-text">Upload Staff</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/uploadmodel.php">
            <i class="fa-solid fa-person"></i>
                <span class="nav-text">Upload Model</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/uploadinfluencer.php">
            <i class="fa-solid fa-person"></i>
                <span class="nav-text">Upload influencer</span>
            </a>
            <a href="../../twinkleadmin/sidenavabar/uploadteam.php">
            <i class="fa-solid fa-user-group"></i>
                <span class="nav-text">Upload Team</span>
            </a>
        </div>
    </nav>

    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" aria-label="Open Navigation">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.querySelector('.side-navbar');
            const toggleBtns = document.querySelectorAll('.toggle-btn, .mobile-toggle');
            
            // Toggle Navbar
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    navbar.classList.toggle('collapsed');
                    navbar.classList.toggle('active');
                });
            });

            // Close navbar when clicking outside on mobile
            document.addEventListener('click', (event) => {
                if (window.innerWidth <= 768 && 
                    navbar.classList.contains('active') && 
                    !navbar.contains(event.target) && 
                    !event.target.closest('.mobile-toggle')) {
                    navbar.classList.remove('active');
                    navbar.classList.add('collapsed');
                }
            });

            // Prevent toggle buttons from closing the navbar when clicked
            toggleBtns.forEach(btn => {
                btn.addEventListener('click', (event) => {
                    event.stopPropagation();
                });
            });
        });
    </script>
</body>
</html>