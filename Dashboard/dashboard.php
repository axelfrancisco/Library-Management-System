<?php include('../../api/admin_check.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../admin.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="admin-layout">
    <div class="sidebar">
      <h2 class="logo">admin</h2>
      <nav class="nav-links">
        <a href="../Dashboard/dashboard.php" class="nav-item active"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
        <a href="../Books/books.php" class="nav-item"><i class="fa-solid fa-book"></i> Books</a>
        <a href="../Users/users.php" class="nav-item"><i class="fa-solid fa-users"></i> Users</a>
        <a href="../Borrowed/borrowed.php" class="nav-item"><i class="fa-solid fa-book-open-reader"></i> Borrowed</a>
        <a href="../Fines/fines.php" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Fines</a>
        <a href="../Reserved/reserved.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> Reserved</a>
        <a href="../../api/admin_logout.php" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </nav>
    </div>

    <main class="page-container">
      <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Visitor statistics with date filters, cards, and employee / college / reason filters.</p>
      </div>

      <div class="toolbar toolbar-grid">
        <select id="rangeFilter">
          <option value="day">Today</option>
          <option value="week">This Week</option>
          <option value="range">Date Range</option>
        </select>
        <input type="date" id="startDate" />
        <input type="date" id="endDate" />
        <select id="reasonFilter"><option value="">All reasons</option></select>
        <select id="collegeFilter"><option value="">All colleges</option></select>
        <select id="employeeFilter">
          <option value="all">All visitors</option>
          <option value="yes">Employees only</option>
          <option value="no">Non-employees only</option>
        </select>
        <button class="primary-btn" id="applyFiltersBtn">Apply Filters</button>
      </div>

      <div class="cards-grid" id="statsCards"></div>

      <div class="table-wrap">
        <h3 class="section-title">Recent visitor logins</h3>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Category</th>
              <th>Reason</th>
              <th>College</th>
              <th>Employee</th>
              <th>Status</th>
              <th>Login Date & Time</th>
            </tr>
          </thead>
          <tbody id="recentLogsBody"></tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="script/dashboard.js"></script>
</body>
</html>
