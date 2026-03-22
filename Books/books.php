<?php include("../../api/admin_check.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Books</title>
  <link rel="stylesheet" href="../admin.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="admin-layout">
    <div class="sidebar">
      <h2 class="logo">admin</h2>
      <nav class="nav-links">
        <a href="../Dashboard/dashboard.php" class="nav-item"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
        <a href="../Books/books.php" class="nav-item active"><i class="fa-solid fa-book"></i> Books</a>
        <a href="../Users/users.php" class="nav-item"><i class="fa-solid fa-users"></i> Users</a>
        <a href="../Borrowed/borrowed.php" class="nav-item"><i class="fa-solid fa-book-open-reader"></i> Borrowed</a>
        <a href="../Fines/fines.php" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Fines</a>
        <a href="../Reserved/reserved.php" class="nav-item"><i class="fa-solid fa-bookmark"></i> Reserved</a>
        <a href="../../api/admin_logout.php" class="nav-item"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </nav>
    </div>

    <main class="page-container">
      <div class="page-header">
        <h1>Books Management</h1>
        <p>Add, edit, and delete books.</p>
      </div>

      <div class="toolbar">
        <input type="text" id="bookSearch" placeholder="Search books..." />
        <button class="primary-btn" id="openAddModalBtn">Add Book</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Cover</th>
              <th>Code</th>
              <th>Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>Status</th>
              <th>New Arrival</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="booksBody"></tbody>
        </table>
      </div>
    </main>
  </div>

  <div class="modal" id="bookModal">
    <div class="modal-card">
      <h2 id="modalTitle">Add Book</h2>
      <div class="form-grid">
        <input id="bookId" type="hidden" />
        <div><input id="bookCode" type="text" placeholder="Book Code" /></div>
        <div><input id="bookTitle" type="text" placeholder="Title" /></div>
        <div><input id="bookAuthor" type="text" placeholder="Author" /></div>
        <div><input id="bookCategory" type="text" placeholder="Category" /></div>
        <div class="full"><input id="bookCover" type="text" placeholder="Cover Image URL" /></div>
        <div>
          <select id="bookStatus">
            <option value="available">available</option>
            <option value="borrowed">borrowed</option>
            <option value="reserved">reserved</option>
            <option value="overdue">overdue</option>
            <option value="new_arrival">new_arrival</option>
          </select>
        </div>
        <div>
          <select id="bookIsNew">
            <option value="0">Not New Arrival</option>
            <option value="1">New Arrival</option>
          </select>
        </div>
      </div>

      <div class="modal-actions">
        <button class="action-btn" id="closeModalBtn">Cancel</button>
        <button class="primary-btn" id="saveBookBtn">Save</button>
      </div>
    </div>
  </div>

  <script src="script/books.js"></script>
</body>
</html>