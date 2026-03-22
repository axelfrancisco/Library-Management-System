<?php include('../../api/admin_check.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Users</title>
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
        <a href="../Users/users.php" class="nav-item active"><i class="fa-solid fa-users"></i> Users</a>
        <a href="../Borrowed/borrowed.php" class="nav-item"><i class="fa-solid fa-book-open-reader"></i> Borrowed</a>
        <a href="../Fines/fines.php" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Fines</a>
        <a href="../Reserved/reserved.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> Reserved</a>
        <a href="../../api/admin_logout.php" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </nav>
    </div>

    <main class="page-container">
      <div class="page-header">
        <h1>Users</h1>
        <p>View users, see last login time, and block or unblock visitors with reasons.</p>
      </div>

      <div class="toolbar">
        <input type="text" id="userSearch" placeholder="Search users..." />
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Roles</th>
              <th>Email</th>
              <th>Category</th>
              <th>College</th>
              <th>Employee</th>
              <th>Last Login</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="usersBody"></tbody>
        </table>
      </div>

      <div class="table-wrap">
        <h3 class="section-title">Visitor login history</h3>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Reason</th>
              <th>College</th>
              <th>Employee</th>
              <th>Status</th>
              <th>Login Time</th>
              <th>Logout Time</th>
            </tr>
          </thead>
          <tbody id="logsBody"></tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="script/users.js"></script>
</body>
</html>
