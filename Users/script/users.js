const usersBody = document.getElementById("usersBody");
const logsBody = document.getElementById("logsBody");
const userSearch = document.getElementById("userSearch");

let allUsers = [];
let allLogs = [];

async function fetchUsers() {
  const res = await fetch("../../api/get_all_users.php");
  const data = await res.json();
  allUsers = data.users || [];
  renderUsers();
}

async function fetchLogs() {
  const res = await fetch("../../api/get_visitor_logs.php");
  const data = await res.json();
  allLogs = data.logs || [];
  renderLogs();
}

function renderUsers(searchValue = "") {
  const keyword = searchValue.toLowerCase().trim();

  const filtered = allUsers.filter(user =>
    (user.full_name || "").toLowerCase().includes(keyword) ||
    (user.email || "").toLowerCase().includes(keyword) ||
    (user.college || "").toLowerCase().includes(keyword)
  );

  usersBody.innerHTML = "";

  if (!filtered.length) {
    usersBody.innerHTML = `<tr><td colspan="10">No users found.</td></tr>`;
    return;
  }

  filtered.forEach(user => {
    const row = document.createElement("tr");
    const statusText = user.is_blocked ? `Blocked: ${user.blocked_reason || "No reason"}` : "Active";
    row.innerHTML = `
      <td>${user.user_id}</td>
      <td>${user.full_name || ""}</td>
      <td>${user.roles || "user"}</td>
      <td>${user.email || ""}</td>
      <td>${user.user_category || ""}</td>
      <td>${user.college || ""}</td>
      <td>${user.is_employee ? "Yes" : "No"}</td>
      <td>${user.last_login || "—"}</td>
      <td>${statusText}</td>
      <td>
        <div class="action-group">
          ${user.is_blocked
            ? `<button class="action-btn" data-action="unblock" data-id="${user.user_id}">Unblock</button>`
            : `<button class="action-btn danger" data-action="block" data-id="${user.user_id}">Block</button>`}
        </div>
      </td>
    `;
    usersBody.appendChild(row);
  });
}

function renderLogs() {
  logsBody.innerHTML = "";
  const logs = allLogs.slice(0, 50);

  if (!logs.length) {
    logsBody.innerHTML = `<tr><td colspan="9">No visitor logs yet.</td></tr>`;
    return;
  }

  logs.forEach(log => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${log.full_name || ""}</td>
      <td>${log.google_email || ""}</td>
      <td>${log.selected_role || ""}</td>
      <td>${log.reason || ""}</td>
      <td>${log.college || ""}</td>
      <td>${log.is_employee ? "Yes" : "No"}</td>
      <td>${log.status}${log.blocked_reason ? ` (${log.blocked_reason})` : ""}</td>
      <td>${log.login_at || ""}</td>
      <td>${log.logout_at || "—"}</td>
    `;
    logsBody.appendChild(row);
  });
}

userSearch.addEventListener("input", () => {
  renderUsers(userSearch.value);
});

usersBody.addEventListener("click", async (event) => {
  const button = event.target.closest("button[data-action]");
  if (!button) return;

  const action = button.dataset.action;
  const userId = Number(button.dataset.id);
  let reason = "";

  if (action === "block") {
    reason = prompt("Enter the reason for blocking this user:");
    if (!reason) return;
  }

  const res = await fetch("../../api/toggle_user_block.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ userId, action, reason })
  });

  const data = await res.json();
  alert(data.message || "Done.");
  await fetchUsers();
  await fetchLogs();
});

fetchUsers();
fetchLogs();
