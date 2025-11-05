<?php
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication helpers
function is_logged_in() {
    return empty($_SESSION['user']);
}

function require_login() {
    if (is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay RIS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/styles.css?v=<?= time() ?>" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/toast.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-md shadow-sm mb-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Barangay RIS</a>

    <!-- Mobile menu toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible nav links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="residents.php">Residents</a></li>
          <li class="nav-item"><a class="nav-link" href="officials.php">Officials</a></li>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="households.php">Households</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="clearances.php">Clearances</a></li>
        <?php endif; ?>
      </ul>

      <!-- Right side: user info / logout -->
      <ul class="navbar-nav">
        <?php if (is_logged_in()): ?>
          <li class="nav-item">
            <span class="nav-link text-muted small">
              👋 <?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?> (<?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?>)
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger fw-semibold" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="toastContainer"></div>
</div>
<div class="container-fluid flex-grow-1">