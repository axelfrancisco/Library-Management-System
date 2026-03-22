<?php include "../../api/auth_check.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Returned Books</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="return.css">

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

        <a href="#" class="nav-item" id="logoutBtn">
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
        Returned Books
    </a>
</h1>
</header>


<div class="search-bar">
<i class="fa-solid fa-search"></i>
<input type="text" id="returnedSearch" placeholder="Search returned books...">
</div>


<div class="returned-table">

<table>

<thead>
<tr>
<th>Cover</th>
<th>Book ID</th>
<th>Book Title</th>
<th>Borrower</th>
<th>Returned Date</th>
<th>Status</th>
</tr>
</thead>

<tbody id="returnedBody"></tbody>

</table>

</div>

</main>

<script src="script/return-system.js"></script>
</body>
</html>