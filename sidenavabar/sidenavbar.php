<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Responsive Side Navigation</title>
    <link rel="stylesheet" href="./sidenavbar.css">
    <!-- Font Awesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <!-- Side Navbar -->
    <nav class="side-navbar">
        <div class="navbar-header">
            <div class="profile-details">
                <h4>Twinkle Media Hub</h4>
                <p>Administrator</p>
            </div>
            <button class="toggle-btn" aria-label="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="nav-links">



            <!-- <a href="./">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="nav-text">Upload Slider</span>
            </a> -->

            <a href="/admin/sidenavabar/work.php">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="nav-text">Upload work video</span>
            </a>
   <a href="/admin/sidenavabar/clienttestmonial.php">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="nav-text">Upload Client Testmonial </span>
            </a>

            <a href="/admin/sidenavabar/creative.php">
                <i class="fas fa-cloud-upload-alt"></i>
                <span class="nav-text">Upload Creative Section </span>
            </a>

            <a href="/admin/sidenavabar/uploadreel.php">
                <i class="fas fa-video"></i>
                <span class="nav-text">Upload Reels</span>
            </a>
    <a href="/admin/sidenavabar/clientlogo.php">
                <i class="fa-solid fa-user-group"></i>
                <span class="nav-text">Upload  Client Logo</span>
            </a>

            <a href="/admin/sidenavabar/uploadstaff.php">
                <i class="fas fa-users"></i>
                <span class="nav-text">Upload Staff</span>
            </a>
            <a href="/admin/sidenavabar/uploadmodel.php">
                <i class="fa-solid fa-person"></i>
                <span class="nav-text">Upload Model</span>
            </a>
            <a href="/admin/sidenavabar/uploadinfluencer.php">
                <i class="fa-solid fa-person"></i>
                <span class="nav-text">Upload influencer</span>
            </a>
            <a href="/admin/sidenavabar/uploadteam.php">
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

            toggleBtns.forEach(btn => {
                btn.addEventListener('click', (event) => {
                    event.stopPropagation(); // prevent closing immediately
                    if (window.innerWidth > 768) {
                        // Desktop: collapse/expand
                        navbar.classList.toggle('collapsed');
                    } else {
                        // Mobile: open/close
                        navbar.classList.toggle('active');
                    }
                });
            });

            // Close on outside click for mobile
            document.addEventListener('click', (event) => {
                if (
                    window.innerWidth <= 768 &&
                    navbar.classList.contains('active') &&
                    !navbar.contains(event.target) &&
                    !event.target.closest('.mobile-toggle')
                ) {
                    navbar.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>