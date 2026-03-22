const rows = document.querySelectorAll(".fine-table tbody tr");
const totalFine = document.getElementById("totalFine");
const unpaidCount = document.getElementById("unpaidCount");
const paidCount = document.getElementById("paidCount");

rows.forEach(row => {
    const actionCell = document.createElement("td");

    const payBtn = document.createElement("button");
    payBtn.className = "pay-btn";
    payBtn.textContent = "Pay";

    payBtn.addEventListener("click", () => {
        if(payBtn.classList.contains("paid")) return;

        payBtn.textContent = "Paid";
        payBtn.classList.add("paid");
        payBtn.disabled = true;

        const statusCell = row.querySelector(".status");
        statusCell.textContent = "Paid";
        statusCell.classList.remove("unpaid");
        statusCell.classList.add("paid");

        updateSummary();
    });

    actionCell.appendChild(payBtn);
    row.appendChild(actionCell);
});

function updateSummary(){
    const fineAmounts = document.querySelectorAll(".fine-table tbody tr .fine-amount");
    const unpaidStatuses = document.querySelectorAll(".fine-table tbody tr .status.unpaid");
    const paidStatuses = document.querySelectorAll(".fine-table tbody tr .status.paid");

    let total = 0;

    fineAmounts.forEach(cell => {
        const row = cell.closest("tr");
        const statusCell = row.querySelector(".status");

        if(statusCell.classList.contains("unpaid")){
            total += Number(cell.dataset.amount);
        }
    });

    totalFine.textContent = `₱${total}`;
    unpaidCount.textContent = unpaidStatuses.length;
    paidCount.textContent = paidStatuses.length;
}