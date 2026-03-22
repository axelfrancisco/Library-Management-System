const reservedBody = document.getElementById("reservedBody");
const reservedSearch = document.getElementById("reservedSearch");

let allReserved = [];

async function fetchReserved() {
  const res = await fetch("../../api/get_all_reserved.php");
  const data = await res.json();
  allReserved = data;
  renderReserved();
}

async function cancelReservation(reserveId) {
  const res = await fetch("../../api/admin_cancel_reservation.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({ reserve_id: reserveId })
  });

  const result = await res.json();
  alert(result.message);
  fetchReserved();
}

function renderReserved(searchValue = "") {
  const keyword = searchValue.toLowerCase().trim();

  const filtered = allReserved.filter(item =>
    item.title.toLowerCase().includes(keyword) ||
    item.reserved_by.toLowerCase().includes(keyword) ||
    item.book_code.toLowerCase().includes(keyword)
  );

  reservedBody.innerHTML = "";

  if (filtered.length === 0) {
    reservedBody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fa-solid fa-bookmark"></i>
            <span>No active reservations found.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filtered.forEach(item => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td><img src="${item.cover_image}" alt="${item.title}"></td>
      <td>${item.book_code}</td>
      <td>${item.title}</td>
      <td>${item.reserved_by}</td>
      <td>${item.reserve_date}</td>
      <td>${item.reserve_status}</td>
      <td>
        <button class="action-btn danger cancel-btn" data-id="${item.reserve_id}">Cancel</button>
      </td>
    `;
    reservedBody.appendChild(row);
  });

  document.querySelectorAll(".cancel-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const ok = confirm("Cancel this reservation?");
      if (!ok) return;
      cancelReservation(btn.dataset.id);
    });
  });
}

reservedSearch.addEventListener("input", () => {
  renderReserved(reservedSearch.value);
});

fetchReserved();