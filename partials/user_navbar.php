<!-- navbar.php -->
<div class="navbar">
    <h2>CozyRack Staff(s) Dashboard</h2>
    <div class="nav-links">
        <a href="edit_profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Navbar CSS -->
<style>
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
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
    }

    .nav-links a:hover {
        text-decoration: underline;
    }
</style>
