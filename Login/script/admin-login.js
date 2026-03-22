const adminEmail = document.getElementById("adminEmail");
const adminLoginBtn = document.getElementById("adminLoginBtn");

adminLoginBtn.addEventListener("click", async () => {
  const email = adminEmail.value.trim();

  if (!email) {
    alert("Please enter admin email.");
    return;
  }

  try {
    const response = await fetch("../../api/admin_login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ email })
    });

    const result = await response.json();

    if (result.success) {
      window.location.href = "../Dashboard/dashboard.php";
    } else {
      alert(result.message || "Admin login failed.");
    }
  } catch (error) {
    console.error(error);
    alert("Something went wrong.");
  }
});