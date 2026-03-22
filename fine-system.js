const fineBody = document.getElementById("fineBody");
const fineSearch = document.getElementById("fineSearch");
const totalFine = document.getElementById("totalFine");
const unpaidCount = document.getElementById("unpaidCount");
const paidCount = document.getElementById("paidCount");

let allFines = [];

async function fetchFines() {
  try {
    const res = await fetch("../../api/get_fines.php");
    const data = await res.json();

    allFines = data;
    renderFineTable();
  } catch (error) {
    console.error("Error fetching fines:", error);
  }
}

async function payFine(fineId) {
  try {
    const response = await fetch("../../api/pay_fine.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        fine_id: fineId
      })
    });

    const result = await response.json();

    if (result.success) {
      alert(result.message);
      await fetchFines();
    } else {
      alert(result.message || "Failed to pay fine.");
    }
  } catch (error) {
    console.error("Pay fine error:", error);
    alert("Something went wrong while paying the fine.");
  }
}

function renderFineTable(searchValue = "") {
  if (!fineBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredFines = allFines.filter(fine =>
    fine.title.toLowerCase().includes(keyword) ||
    fine.borrower.toLowerCase().includes(keyword) ||
    fine.id.toLowerCase().includes(keyword) ||
    fine.status.toLowerCase().includes(keyword)
  );

  fineBody.innerHTML = "";

  if (filteredFines.length === 0) {
    fineBody.innerHTML = `
      <tr>
        <td colspan="9">
          <div class="empty-state">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>No fines found.</span>
          </div>
        </td>
      </tr>
    `;
    updateSummary([]);
    return;
  }

  filteredFines.forEach(fine => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${fine.cover}" alt="${fine.title}"></td>
      <td>#${fine.id}</td>
      <td>${fine.title}</td>
      <td>${fine.borrower}</td>
      <td>${fine.dueDate}</td>
      <td>${fine.daysLate}</td>
      <td class="fine-amount">₱${fine.amount}</td>
      <td class="${fine.status === "paid" ? "paid" : "unpaid"}">
        ${fine.status === "paid" ? "Paid" : "Unpaid"}
      </td>
      <td>
        <button class="pay-btn ${fine.status === "paid" ? "paid" : ""}" data-fine-id="${fine.fine_id}" ${fine.status === "paid" ? "disabled" : ""}>
          ${fine.status === "paid" ? "Paid" : "Pay"}
        </button>
      </td>
    `;

    fineBody.appendChild(row);
  });

  document.querySelectorAll(".pay-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const confirmPay = confirm("Are you sure you want to pay this fine?");
      if (!confirmPay) return;

      payFine(btn.dataset.fineId);
    });
  });

  updateSummary(filteredFines);
}

function updateSummary(fines) {
  const unpaidFines = fines.filter(fine => fine.status === "unpaid");
  const paidFines = fines.filter(fine => fine.status === "paid");

  const total = unpaidFines.reduce((sum, fine) => sum + Number(fine.amount), 0);

  if (totalFine) totalFine.textContent = `₱${total}`;
  if (unpaidCount) unpaidCount.textContent = unpaidFines.length;
  if (paidCount) paidCount.textContent = paidFines.length;
}

if (fineSearch) {
  fineSearch.addEventListener("input", () => {
    renderFineTable(fineSearch.value);
  });
}

fetchFines();