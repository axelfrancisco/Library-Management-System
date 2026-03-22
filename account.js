function saveProfile() {
  const data = {
    name: document.getElementById("userName").value,
    course: document.getElementById("userCourse").value,
    year: document.getElementById("yearLevel").value,
    contact: document.getElementById("contactNumber").value,
    address: document.getElementById("userAddress").value
  };

  fetch("../../api/update_profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      alert("Profile updated!");
    } else {
      alert("Error updating profile");
      console.error(res);
    }
  })
  .catch(err => {
    console.error("Fetch error:", err);
  });
}

function loadProfile() {
  fetch("../../api/get_profile.php")
    .then(res => res.json())
    .then(res => {
      if (!res.success) return;

      const user = res.data;

      document.getElementById("userName").value = user.name || "";
      document.getElementById("userCourse").value = user.course || "";
      document.getElementById("yearLevel").value = user.yearLevel || "";
      document.getElementById("contactNumber").value = user.contactNumber || "";
      document.getElementById("userAddress").value = user.address || "";
    });
}

// 🔥 connect button
document.getElementById("saveProfileBtn")
  .addEventListener("click", saveProfile);

loadProfile();