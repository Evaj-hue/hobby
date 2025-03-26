<!-- navbar.php -->
<div class="navbar">
    <h2>CozyRack Management</h2>
    <div class="nav-links">
        <a href="profile.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
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
    z-index: 9999;  /* Ensure the navbar stays on top */
}
/* Lower navbar z-index when modal is open */
.modal-open .navbar {
        z-index: 0 !important; /* Pushes navbar behind the modal */
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
    margin-top: 60px;  /* Same height as the navbar */
    padding: 20px;  /* Add padding to the content area */
}
</style>
