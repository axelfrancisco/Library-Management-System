<?php include("../../api/auth_check.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account</title>

  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="account.css" />
</head>
<body>
  <div class="sidebar">
    <h2 class="logo">menu</h2>

    <nav class="nav-links">
      <a href="../My Account/account.php" class="nav-item active">
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

      <a href="../../api/logout.php" class="nav-item">
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
          My Account
        </a>
      </h1>
    </header>

    <section class="account-card">
      <div class="profile-top">
        <div class="profile-avatar-wrap">
          <div class="profile-avatar">
            <img id="profileImage" src="../../img/default-profile.png" alt="Profile" />
            <div class="avatar-fallback" id="avatarFallback">
              <i class="fa-solid fa-user"></i>
            </div>
          </div>

          <label for="profileUpload" class="upload-btn">
            <i class="fa-solid fa-camera"></i>
            Change Photo
          </label>
          <input type="file" id="profileUpload" accept="image/*" hidden />
        </div>

        <div class="profile-main-info">
          <input type="text" id="userName" value="Juan Dela Cruz" />
          <input type="text" id="userRole" value="Student Member" />
        </div>

        <button class="save-btn" id="saveProfileBtn">
          <i class="fa-solid fa-floppy-disk"></i>
          Save Profile
        </button>
      </div>

      <div class="info-grid">
        <div class="info-box">
          <span class="info-label">Student ID</span>
          <input type="text" id="studentId" value="2026-00124" />
        </div>

        <div class="info-box">
          <span class="info-label">Email</span>
          <input type="email" id="userEmail" value="juandelacruz@email.com" />
        </div>

        <div class="info-box">
          <span class="info-label">Course</span>
          <input type="text" id="userCourse" value="BS Information Technology" />
        </div>

        <div class="info-box">
          <span class="info-label">Year Level</span>
          <input type="text" id="yearLevel" value="2nd Year" />
        </div>

        <div class="info-box">
          <span class="info-label">Contact Number</span>
          <input type="text" id="contactNumber" value="09123456789" />
        </div>

        <div class="info-box full-width">
          <span class="info-label">Address</span>
          <input type="text" id="userAddress" value="Quezon City, Philippines" />
        </div>
      </div>
    </section>
  </main>

  <script src="script/account.js"></script>
</body>
</html>