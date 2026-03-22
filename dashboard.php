<?php include '../../api/auth_check.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Library Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="dashboard.css" />
  </head>
  <body>
    <div class="sidebar">
      <h2 class="logo">menu</h2>
      <nav class="nav-links">
        <a href="../My Account/account.php" class="nav-item"><i class="fa-solid fa-user"></i> My Account</a>
        <a href="../Liked Books/liked.php" class="nav-item"><i class="fa-solid fa-heart"></i> Liked Books</a>
        <a href="../Fine/fine.php" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Fine</a>
        <a href="../../api/logout.php" class="nav-item" id="logoutBtn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log out</a>
      </nav>
    </div>

    <main class="dashboard-container">
      <header class="dashboard-header">
        <h1>Dashboard</h1>
        <h2>WELCOME TO NEU LIBRARY!</h2>
        <p class="welcome-line">Hello, <?php echo htmlspecialchars($_SESSION['auth_user_name'] ?? 'User'); ?>.</p>
      </header>

      <div class="cards-grid">
        <a href="../Returned Books/return.php" class="card"><i class="fa-solid fa-rotate-left card-icon"></i><p>Returned Books</p></a>
        <a href="../Borrowed Books/borrow.php" class="card"><i class="fa-solid fa-book-open-reader card-icon"></i><p>Borrowed Books</p></a>
        <a href="../Available Books/available.php" class="card"><i class="fa-solid fa-book-circle-check card-icon"></i><i class="fa-solid fa-book card-icon"></i><p>Available Books</p></a>
        <a href="../Overdue Books/overdue.php" class="card"><i class="fa-solid fa-clock card-icon"></i><p>Overdue Books</p></a>
        <a href="../Reserved Books/reserved.php" class="card"><i class="fa-solid fa-bookmark card-icon"></i><p>Reserved Books</p></a>
        <a href="../New Arrival Books/newarrival.php" class="card"><i class="fa-solid fa-star card-icon"></i><p>New Arrival Books</p></a>
      </div>
    </main>

    <script src="../../script/logout.js"></script>
  </body>
</html>
