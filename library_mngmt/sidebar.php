<div class="sidebar" id="sidebar">
    <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
        <i class="bi bi-list"></i> <span class="btn-text">Dashboard</span>
    </button>
    <br><br>
    <button onclick="window.location.href='index.php'" id="home-btn" class="home-btn">
        <i class="bi bi-house-door"></i> <span class="btn-text">| Home</span>
    </button>
    <button onclick="window.location.href='view_books.php'" id="books-btn">
        <i class="bi bi-book"></i> <span class="btn-text">| Book Inventory</span>
    </button>
    <button onclick="window.location.href='view_borrowers.php'" id="users-btn">
        <i class="bi bi-people-fill"></i> <span class="btn-text">| User Record</span>
    </button>
    <button onclick="window.location.href='transaction.php'" id="transactions-btn">
        <i class="bi bi-journal-text"></i> <span class="btn-text">| Transaction Log</span>
    </button>
    <br><br><br><br><br><br><br><br><br><br><br><br><br>
    <button onclick="window.location.href='register.php'">
        <i class="bi bi-box-arrow-right"></i> <span class="btn-text">| Log Out</span>
    </button>
</div>

<style>
    .sidebar {
        width: 250px;
        background: #342519;
        color: #ede6d9;
        padding-top: 20px;
        position: fixed;
        height: 100%;
    }
    .sidebar button {
        width: 100%;
        text-align: left;
        padding: 15px;
        border: none;
        background: transparent;
        color: white;
        font-size: 1.1rem;
    }
    .sidebar button:hover {
        background: #ede6e9;
        color: #342519;
    }
    .sidebar .btn-text {
        margin-left: 11px;
    }
    .sidebar.collapsed {
        width: 60px;
    }
    .sidebar.collapsed .btn-text {
        display: none;
    }
    .sidebar.collapsed button {
        padding-top: 19px;
        padding-bottom: 19px;
    }
    .sidebar .sidebar-toggle-btn {
        top: 20px;
        left: 20px;
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
    }
    .main-content-area {
        margin-left: 250px;
        padding: 20px;
        flex-grow: 1;
        width: calc(100% - 250px);
        overflow-y: auto;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }
    .sidebar.collapsed + .main-content-area {
        margin-left: 60px;
        width: calc(100% - 60px);
    }
    .active-sidebar-btn {
        background-color: #ede6d9 !important;
        color: #342519 !important;
    }
    @media (max-width: 768px) {
        .sidebar button {
            text-align: center;
        }
        .sidebar.collapsed button i {
            font-size: 1.5rem;
        }
        .main-content-area {
            margin-left: 60px;
        }
    }
</style>

<script>
    function toggleSidebar() {
        let sidebar = document.getElementById('sidebar');
        let mainContent = document.getElementById('main-content');
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('hidden');
            mainContent.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '250px';
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.style.marginLeft = sidebar.classList.contains('collapsed') ? '60px' : '250px';
        }
    }
</script>
