<!-- sidebar.php -->
<div class="sidebar">
    <div class="sidebar-logo">
        <img src="../src/page-logo.png" alt="Company Logo">
    </div>
    <a href="../admin/admin_dashboard.php">Dashboard</a>
    <a href="../rack/rack_dashboard.php">Manage Racks</a>
    <a href="../admin/manage_roles.php">Role Management</a>
    <a href="../admin/manage_products.php">Manage Products</a>
    <a href="../admin/manage_merch.php">Manage Merch</a>
    <a href="../admin/activity_logs.php">Reports</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Sidebar CSS -->
<style>
    /* Sidebar Styling */
    .sidebar {
        width: 200px;
        background-color: #362532; /* CozyRack sidebar */
        padding: 20px;
        height: 100vh;
        box-sizing: border-box;
        border-right: 2px solid #362532; /* Highlight border */
        position: fixed;
        top: 0;
        left: 0;
        transition: transform 0.3s ease; /* Smooth transition for responsiveness */
        z-index: 1020; /* Ensure sidebar stays below navbar */
    }

    .sidebar-logo {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #ED7117; /* Orange highlight */
    }

    .sidebar-logo img {
        width: 100%;
        max-width: 100px;
        display: block;
        margin: 0 auto;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        background: transparent;
        transition: 0.3s;
    }

    .sidebar a:hover, .sidebar a.active {
        background: #ED7117; /* Highlight */
    }

    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%); /* Hide sidebar off-canvas */
        }

        .sidebar.open {
            transform: translateX(0); /* Show sidebar */
        }

        .sidebar a {
            font-size: 14px; /* Adjust link size for smaller screens */
        }
    }
</style>