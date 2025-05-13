<!-- navbar.php -->
<div class="navbar">
    <h2>CozyRack Staff(s) Dashboard</h2>
    <div class="nav-links">
        <a href="edit_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<!-- Navbar CSS -->
<style>
     /* Navbar Styling */
    .navbar {
        width: calc(100% - 200px); /* Adjust for sidebar width */
        height: 60px;
        background: #253529; /* Dark theme */
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        position: fixed;
        top: 0;
        left: 200px; /* Offset to align with sidebar */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1030; /* Ensure navbar stays above other elements */
        transition: left 0.3s ease; /* Smooth transition for responsiveness */
    }

    /* Navbar link styling */
    .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
    }

    .nav-links a:hover {
        text-decoration: underline;
    }

    /* Content styling to avoid overlap with navbar */
    .content {
        margin-top: 60px; /* Same height as the navbar */
        padding: 20px; /* Add padding to the content area */
    }

    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .navbar {
            width: 100%; /* Full width */
            left: 0; /* Align navbar with hidden sidebar */
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap; /* Ensure links wrap on smaller screens */
        }

        .nav-links a {
            margin-left: 10px; /* Reduce space for smaller screens */
        }
    }
</style>