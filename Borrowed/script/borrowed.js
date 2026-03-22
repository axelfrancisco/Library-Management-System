const borrowedBody = document.getElementById("borrowedBody");
const borrowedSearch = document.getElementById("borrowedSearch");

let allBorrowed = [];

async function fetchBorrowed() {
  const res = await fetch("../../api/get_all_borrowed.php");
  const data = await res.json();
  allBorrowed = data;
  renderBorrowed();
}

async function forceReturn(borrowId) {
  const res = await fetch("../../api/admin_force_return.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({ borrow_id: borrowId })
  });

  const result = await res.json();
  alert(result.message);
  fetchBorrowed();
}

function renderBorrowed(searchValue = "") {
  const keyword = searchValue.toLowerCase().trim();

  const filtered = allBorrowed.filter(item =>
    item.title.toLowerCase().includes(keyword) ||
    item.borrower.toLowerCase().includes(keyword) ||
    item.book_code.toLowerCase().includes(keyword)
  );

  borrowedBody.innerHTML = "";

  if (filtered.length === 0) {
    borrowedBody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>No borrowed books found.</span>
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
      <td>${item.borrower}</td>
      <td>${item.borrow_date}</td>
      <td>${item.due_date}</td>
      <td>
        <button class="action-btn warn return-btn" data-id="${item.borrow_id}">Force Return</button>
      </td>
    `;
    borrowedBody.appendChild(row);
  });

  document.querySelectorAll(".return-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const ok = confirm("Force return this book?");
      if (!ok) return;
      forceReturn(btn.dataset.id);
    });
  });
}

borrowedSearch.addEventListener("input", () => {
  renderBorrowed(borrowedSearch.value);
});

fetchBorrowed();