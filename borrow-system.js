const borrowedBody = document.getElementById("borrowedBody");
const borrowedSearch = document.getElementById("borrowedSearch");

let allBorrowedBooks = [];

async function fetchBorrowedBooks() {
  try {
    const res = await fetch("../../api/get_borrowed_books.php");
    const data = await res.json();

    allBorrowedBooks = data;
    renderBorrowedBooks();
  } catch (error) {
    console.error("Error fetching borrowed books:", error);
  }
}

function updateBorrowedCount(filteredCount = null) {
  const count = filteredCount !== null ? filteredCount : allBorrowedBooks.length;
  const el = document.getElementById("borrowedCount");

  if (el) el.textContent = count;
}

async function returnBook(borrowId) {
  try {
    const response = await fetch("../../api/return_book.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        borrow_id: borrowId
      })
    });

    const result = await response.json();

    if (result.success) {
      await fetchBorrowedBooks();
      alert(result.message);
    } else {
      alert(result.message || "Failed to return book.");
    }
  } catch (error) {
    console.error("Return error:", error);
    alert("Something went wrong while returning the book.");
  }
}

function renderBorrowedBooks(searchValue = "") {
  if (!borrowedBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredBooks = allBorrowedBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.borrower.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  borrowedBody.innerHTML = "";

  if (filteredBooks.length === 0) {
    borrowedBody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>No borrowed books yet.</span>
          </div>
        </td>
      </tr>
    `;
    updateBorrowedCount(0);
    return;
  }

  filteredBooks.forEach(book => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.borrower}</td>
      <td>${book.borrowDate}</td>
      <td class="borrowed">Borrowed</td>
      <td>
        <div class="action-cell">
          <button class="return-btn" data-borrow-id="${book.borrow_id}">Return</button>
        </div>
      </td>
    `;

    borrowedBody.appendChild(row);
  });

  document.querySelectorAll(".return-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const confirmReturn = confirm("Are you sure you want to return this book?");
      if (!confirmReturn) return;

      returnBook(btn.dataset.borrowId);
    });
  });

  updateBorrowedCount(filteredBooks.length);
}

if (borrowedSearch) {
  borrowedSearch.addEventListener("input", () => {
    renderBorrowedBooks(borrowedSearch.value);
  });
}

fetchBorrowedBooks();