<?php include "../../api/auth_check.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Overdue Books</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="overdue.css">

</head>

<body>

<div class="sidebar">
    <h2 class="logo">menu</h2>

    <nav class="nav-links">

        <a href="../My Account/account.php" class="nav-item">
        <i class="fa-solid fa-user"></i>
        My Account
        </a>

        <a href="../Liked Books/liked.php" class="nav-item">
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
        <a href="../Dashboard/dashboard.php" style="text-decoration: none; color: inherit;">
            <i class="fa-solid fa-rotate-left"></i> 
            Overdue Books
        </a>
    </h1>
</header>

<div class="search-bar">
    <i class="fa-solid fa-search"></i>
    <input type="text" placeholder="Search overdue books...">
</div>

<div class="overdue-table">

<table>

<thead>
<tr>
<th>Cover</th>
<th>Book ID</th>
<th>Book Title</th>
<th>Borrower</th>
<th>Due Date</th>
<th>Status</th>
</tr>
</thead>

<tbody id="overdueBody"></tbody>

</table>

</div>

</main>

<script src="script/overdue-system.js"></script>
</body>
</html>