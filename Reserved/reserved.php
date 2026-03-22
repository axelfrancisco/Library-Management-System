<?php include("../../api/admin_check.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Reserved</title>
  <link rel="stylesheet" href="../admin.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="admin-layout">
    <div class="sidebar">
      <h2 class="logo">admin</h2>
      <nav class="nav-links">
        <a href="../Dashboard/dashboard.php" class="nav-item"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
        <a href="../Books/books.php" class="nav-item"><i class="fa-solid fa-book"></i> Books</a>
        <a href="../Users/users.php" class="nav-item"><i class="fa-solid fa-users"></i> Users</a>
        <a href="../Borrowed/borrowed.php" class="nav-item"><i class="fa-solid fa-book-open-reader"></i> Borrowed</a>
        <a href="../Fines/fines.php" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Fines</a>
        <a href="../Reserved/reserved.php" class="nav-item active"><i class="fa-solid fa-bookmark"></i> Reserved</a>
        <a href="../../api/admin_logout.php" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </nav>
    </div>

    <main class="page-container">
      <div class="page-header">
        <h1>Reserved Books</h1>
        <p>View and cancel active reservations.</p>
      </div>

      <div class="toolbar">
        <input type="text" id="reservedSearch" placeholder="Search reservations..." />
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Cover</th>
              <th>Code</th>
              <th>Title</th>
              <th>Reserved By</th>
              <th>Reserve Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="reservedBody"></tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="script/reserved.js"></script>
</body>
</html>