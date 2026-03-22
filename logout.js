const logoutBtn = document.getElementById("logoutBtn");

if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const confirmLogout = confirm("Are you sure you want to log out?");

        if (confirmLogout) {
            localStorage.clear();
            window.location.href = "/src/Users/Login Account/index.html";
        }
    });
}