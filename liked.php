<?php include "../../api/auth_check.php"; ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Liked Books</title>

    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="liked.css" />
  </head>

  <body>
    <div class="sidebar">
      <h2 class="logo">menu</h2>

      <nav class="nav-links">
        <a href="../My Account/account.php" class="nav-item">
          <i class="fa-solid fa-user"></i>
          My Account
        </a>

        <a href="../Liked Books/liked.php" class="nav-item active">
          <i class="fa-solid fa-heart"></i>
          Liked Books
        </a>

        <a href="../Fine/fine.php" class="nav-item">
          <i class="fa-solid fa-file-invoice-dollar"></i>
          Fine
        </a>

        <a href="../../api/logout.php" class="nav-item" id="logoutBtn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          Log out
        </a>
      </nav>
    </div>

    <main class="page-container">
      <header class="page-header">
        <h1>
          <a href="../Dashboard/dashboard.php">
            <i class="fa-solid fa-rotate-left"></i>
            Liked Books
          </a>
        </h1>
      </header>

      <div class="summary-box">
        <i class="fa-solid fa-heart"></i>
        <div>
          <span>Total Liked Books</span>
          <p id="likedCount">0</p>
        </div>
      </div>

      <div class="search-bar">
        <i class="fa-solid fa-search"></i>
        <input
          type="text"
          id="likedSearch"
          placeholder="Search liked books..."
        />
      </div>

      <div class="liked-table">
        <table>
          <thead>
            <tr>
              <th>Cover</th>
              <th>Book ID</th>
              <th>Book Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody id="likedBody"></tbody>
        </table>
      </div>
    </main>

    <script src="script/liked-system.js"></script>
  </body>
</html>
