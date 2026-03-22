const statusBox = document.getElementById("loginStatus");
const detailsForm = document.getElementById("detailsForm");
const profilePreview = document.getElementById("profilePreview");
const roleCards = document.getElementById("roleCards");
const reasonCards = document.getElementById("reasonCards");
const roleBlock = document.getElementById("roleBlock");
const reasonBlock = document.getElementById("reasonBlock");
const userCategory = document.getElementById("userCategory");
const college = document.getElementById("college");
const isEmployee = document.getElementById("isEmployee");

let googleProfile = null;
let selectedRole = "user";
let selectedReason = "";

function setStatus(message, type = "") {
  statusBox.textContent = message;
  statusBox.className = `status-message ${type}`.trim();
}

function makeSelectableCards(container, values, onSelect, formatter = (v) => v) {
  container.innerHTML = "";
  values.forEach((value, index) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "card" + (index === 0 ? " active" : "");
    btn.dataset.value = value;
    btn.innerHTML = formatter(value);
    btn.addEventListener("click", () => {
      container.querySelectorAll(".card").forEach((card) => card.classList.remove("active"));
      btn.classList.add("active");
      onSelect(value);
    });
    container.appendChild(btn);
  });
  if (values.length) onSelect(values[0]);
}

function renderProfile(profile) {
  const roles = (profile.roles || []).map((role) => role === "admin" ? "Admin" : "Regular User").join(" / ");
  profilePreview.innerHTML = `
    <strong>${profile.name}</strong>
    <span>${profile.email}</span><br>
    <span>Allowed access: ${roles || "Regular User"}</span>
  `;
}

function handleRoleUI() {
  const adminMode = selectedRole === "admin";
  reasonBlock.style.display = adminMode ? "none" : "block";
  userCategory.disabled = false;
  college.disabled = false;
  isEmployee.disabled = false;
}

async function handleGoogleResponse(response) {
  setStatus("Verifying Google account...", "");
  try {
    const res = await fetch("../../api/google_login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ credential: response.credential })
    });

    // Check if response is OK before parsing JSON
    if (!res.ok && res.status !== 401 && res.status !== 403 && res.status !== 422 && res.status !== 500) {
      setStatus(`Server error (${res.status}). Please try again.`, "error");
      return;
    }

    let data;
    try {
      data = await res.json();
    } catch (parseErr) {
      // Server returned non-JSON (e.g. PHP error page)
      const raw = await res.text().catch(() => "");
      console.error("Non-JSON response from google_login.php:", raw);
      setStatus("Server configuration error. Check PHP error logs.", "error");
      return;
    }

    if (!data.success) {
      setStatus(data.message || "Google sign-in failed.", "error");
      return;
    }

    googleProfile = data.profile;
    renderProfile(googleProfile);

    const roles = googleProfile.roles && googleProfile.roles.length ? googleProfile.roles : ["user"];
    roleBlock.style.display = roles.length > 1 ? "block" : "none";
    makeSelectableCards(roleCards, roles, (value) => {
      selectedRole = value;
      handleRoleUI();
    }, (value) => value === "admin" ? "Admin Access" : "Regular User");

    detailsForm.classList.remove("hidden");

    if (googleProfile.blocked && googleProfile.blocked.blocked) {
      setStatus(`This account is blocked for regular-user access. Reason: ${googleProfile.blocked.reason}`, "error");
    } else {
      setStatus("Google account verified. Complete the visitor details.", "success");
    }
  } catch (error) {
    console.error("Google sign-in error:", error);
    setStatus("Network error during Google sign-in. Check your connection and try again.", "error");
  }
}

function initializeGoogle() {
  if (!window.google || !window.google.accounts || !window.google.accounts.id) {
    setStatus("Google script failed to load. Check your internet connection.", "error");
    return;
  }

  const clientId = window.NEU_GOOGLE_CLIENT_ID || "";
  if (!clientId || clientId.includes("YOUR_GOOGLE_WEB_CLIENT_ID")) {
    setStatus("Set your Google client ID in src/config/google.php before testing live sign-in.", "error");
    return;
  }

  window.google.accounts.id.initialize({
    client_id: clientId,
    callback: handleGoogleResponse
  });

  window.google.accounts.id.renderButton(
    document.getElementById("googleSignInButton"),
    { theme: "outline", size: "large", text: "continue_with", shape: "pill", width: 320 }
  );
}

reasonCards.querySelectorAll(".card").forEach((card) => {
  card.addEventListener("click", () => {
    reasonCards.querySelectorAll(".card").forEach((c) => c.classList.remove("active"));
    card.classList.add("active");
    selectedReason = card.dataset.value;
  });
});

selectedReason = reasonCards.querySelector(".card")?.dataset.value || "";
if (reasonCards.querySelector(".card")) {
  reasonCards.querySelector(".card").classList.add("active");
}

handleRoleUI();
window.addEventListener("load", initializeGoogle);

detailsForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  if (!googleProfile) {
    setStatus("Sign in with Google first.", "error");
    return;
  }

  if (selectedRole === "user" && (!userCategory.value || !selectedReason)) {
    setStatus("Choose an account type and reason for visiting.", "error");
    return;
  }

  try {
    const res = await fetch("../../api/complete_login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        selectedRole,
        userCategory: userCategory.value,
        reason: selectedReason,
        college: college.value.trim(),
        isEmployee: isEmployee.checked
      })
    });

    let data;
    try {
      data = await res.json();
    } catch (parseErr) {
      const raw = await res.text().catch(() => "");
      console.error("Non-JSON response from complete_login.php:", raw);
      setStatus("Server error while completing login. Check PHP error logs.", "error");
      return;
    }

    if (!data.success) {
      setStatus(data.message || "Unable to complete login.", "error");
      return;
    }

    setStatus("Login complete. Redirecting...", "success");
    window.location.href = data.redirect;
  } catch (error) {
    console.error("Complete login error:", error);
    setStatus("Network error while saving visitor log. Try again.", "error");
  }
});
