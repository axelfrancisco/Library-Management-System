const finesBody = document.getElementById("finesBody");
const fineSearch = document.getElementById("fineSearch");

let allFines = [];

async function fetchFines() {
  const res = await fetch("../../api/get_all_fines.php");
  const data = await res.json();
  allFines = data;
  renderFines();
}

async function markPaid(fineId) {
  const res = await fetch("../../api/admin_mark_fine_paid.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({ fine_id: fineId })
  });

  const result = await res.json();
  alert(result.message);
  fetchFines();
}

function renderFines(searchValue = "") {
  const keyword = searchValue.toLowerCase().trim();

  const filtered = allFines.filter(item =>
    item.title.toLowerCase().includes(keyword) ||
    item.borrower.toLowerCase().includes(keyword) ||
    item.book_code.toLowerCase().includes(keyword)
  );

  finesBody.innerHTML = "";

  if (filtered.length === 0) {
    finesBody.innerHTML = `
      <tr>
        <td colspan="8">
          <div class="empty-state">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>No fines found.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filtered.forEach(item => {
    const isPaid = item.payment_status === "paid";

    const row = document.createElement("tr");
    row.innerHTML = `
      <td><img src="${item.cover_image}" alt="${item.title}"></td>
      <td>${item.book_code}</td>
      <td>${item.title}</td>
      <td>${item.borrower}</td>
      <td>${item.days_late}</td>
      <td>₱${item.amount}</td>
      <td>${isPaid ? "Paid" : "Unpaid"}</td>
      <td>
        <button class="action-btn ${isPaid ? "" : "warn"} mark-btn" data-id="${item.fine_id}" ${isPaid ? "disabled" : ""}>
          ${isPaid ? "Paid" : "Mark Paid"}
        </button>
      </td>
    `;
    finesBody.appendChild(row);
  });

  document.querySelectorAll(".mark-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      if (btn.disabled) return;
      const ok = confirm("Mark this fine as paid?");
      if (!ok) return;
      markPaid(btn.dataset.id);
    });
  });
}

fineSearch.addEventListener("input", () => {
  renderFines(fineSearch.value);
});

fetchFines();