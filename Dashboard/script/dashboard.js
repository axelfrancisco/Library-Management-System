const statsCards = document.getElementById("statsCards");
const recentLogsBody = document.getElementById("recentLogsBody");
const rangeFilter = document.getElementById("rangeFilter");
const startDate = document.getElementById("startDate");
const endDate = document.getElementById("endDate");
const reasonFilter = document.getElementById("reasonFilter");
const collegeFilter = document.getElementById("collegeFilter");
const employeeFilter = document.getElementById("employeeFilter");
const applyFiltersBtn = document.getElementById("applyFiltersBtn");

function buildQuery() {
  const params = new URLSearchParams({
    range: rangeFilter.value,
    employee: employeeFilter.value,
  });
  if (startDate.value) params.set("start_date", startDate.value);
  if (endDate.value) params.set("end_date", endDate.value);
  if (reasonFilter.value) params.set("reason", reasonFilter.value);
  if (collegeFilter.value) params.set("college", collegeFilter.value);
  return params.toString();
}

function renderStats(stats) {
  const cards = [
    { icon: "fa-users", label: "Total Visits", value: stats.total_visits },
    { icon: "fa-user-check", label: "Unique Visitors", value: stats.unique_visitors },
    { icon: "fa-id-badge", label: "Employee Visits", value: stats.employee_visits },
    { icon: "fa-user", label: "Non-Employee Visits", value: stats.non_employee_visits },
    { icon: "fa-clipboard-list", label: `Top Reason: ${stats.top_reason}`, value: stats.top_reason_total },
    { icon: "fa-building-columns", label: `Top College: ${stats.top_college}`, value: stats.top_college_total },
  ];

  statsCards.innerHTML = "";
  cards.forEach((card) => {
    const div = document.createElement("div");
    div.className = "card";
    div.innerHTML = `
      <i class="fa-solid ${card.icon}"></i>
      <h3>${card.value}</h3>
      <p>${card.label}</p>
    `;
    statsCards.appendChild(div);
  });
}

function fillSelect(select, items, label) {
  const current = select.value;
  select.innerHTML = `<option value="">${label}</option>`;
  items.forEach((item) => {
    const option = document.createElement("option");
    option.value = item;
    option.textContent = item;
    if (item === current) option.selected = true;
    select.appendChild(option);
  });
}

async function loadStats() {
  const res = await fetch(`../../api/get_admin_stats.php?${buildQuery()}`);
  const data = await res.json();
  if (!data.success) return;

  renderStats(data.stats);
  fillSelect(reasonFilter, data.filters.reasons || [], "All reasons");
  fillSelect(collegeFilter, data.filters.colleges || [], "All colleges");
}

async function loadRecentLogs() {
  const res = await fetch(`../../api/get_visitor_logs.php`);
  const data = await res.json();
  if (!data.success) return;

  recentLogsBody.innerHTML = "";
  const logs = (data.logs || []).slice(0, 10);

  if (!logs.length) {
    recentLogsBody.innerHTML = `<tr><td colspan="8">No visitor logs yet.</td></tr>`;
    return;
  }

  logs.forEach((log) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${log.full_name || ""}</td>
      <td>${log.google_email || ""}</td>
      <td>${log.user_category || ""}</td>
      <td>${log.reason || ""}</td>
      <td>${log.college || ""}</td>
      <td>${log.is_employee ? "Yes" : "No"}</td>
      <td>${log.status}${log.blocked_reason ? ` (${log.blocked_reason})` : ""}</td>
      <td>${log.login_at || ""}</td>
    `;
    recentLogsBody.appendChild(row);
  });
}

applyFiltersBtn.addEventListener("click", async () => {
  await loadStats();
});

loadStats();
loadRecentLogs();
