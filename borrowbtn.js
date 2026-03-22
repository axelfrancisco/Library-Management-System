const rows = document.querySelectorAll(".available-table tbody tr");

rows.forEach(row => {
    const cell = document.createElement("td");
    cell.className = "action-cell";

    const borrowBtn = document.createElement("button");
    borrowBtn.className = "borrow-btn";
    borrowBtn.textContent = "Borrow";

    const likeBtn = document.createElement("button");
    likeBtn.className = "like-btn";
    likeBtn.innerHTML = '<i class="fa-solid fa-heart"></i>';

    likeBtn.addEventListener("click", () => {
        likeBtn.classList.toggle("liked");
    });

    cell.appendChild(borrowBtn);
    cell.appendChild(likeBtn);
    row.appendChild(cell);
});