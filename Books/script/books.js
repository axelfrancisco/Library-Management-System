const booksBody = document.getElementById("booksBody");
const bookSearch = document.getElementById("bookSearch");
const openAddModalBtn = document.getElementById("openAddModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const saveBookBtn = document.getElementById("saveBookBtn");
const bookModal = document.getElementById("bookModal");
const modalTitle = document.getElementById("modalTitle");

const bookId = document.getElementById("bookId");
const bookCode = document.getElementById("bookCode");
const bookTitle = document.getElementById("bookTitle");
const bookAuthor = document.getElementById("bookAuthor");
const bookCategory = document.getElementById("bookCategory");
const bookCover = document.getElementById("bookCover");
const bookStatus = document.getElementById("bookStatus");
const bookIsNew = document.getElementById("bookIsNew");

let allBooks = [];

async function fetchBooks() {
  const res = await fetch("../../api/get_all_books.php");
  const data = await res.json();
  allBooks = data;
  renderBooks();
}

function renderBooks(searchValue = "") {
  const keyword = searchValue.toLowerCase().trim();

  const filtered = allBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.author.toLowerCase().includes(keyword) ||
    book.book_code.toLowerCase().includes(keyword)
  );

  booksBody.innerHTML = "";

  if (filtered.length === 0) {
    booksBody.innerHTML = `
      <tr>
        <td colspan="8">
          <div class="empty-state">
            <i class="fa-solid fa-book"></i>
            <span>No books found.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filtered.forEach(book => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td><img src="${book.cover_image}" alt="${book.title}"></td>
      <td>${book.book_code}</td>
      <td>${book.title}</td>
      <td>${book.author}</td>
      <td>${book.category || ""}</td>
      <td>${book.status}</td>
      <td>${book.is_new_arrival == 1 ? "Yes" : "No"}</td>
      <td>
        <div class="action-group">
          <button class="action-btn warn edit-btn" data-id="${book.book_id}">Edit</button>
          <button class="action-btn danger delete-btn" data-id="${book.book_id}">Delete</button>
        </div>
      </td>
    `;
    booksBody.appendChild(row);
  });

  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const book = allBooks.find(item => item.book_id == btn.dataset.id);
      if (!book) return;

      modalTitle.textContent = "Edit Book";
      bookId.value = book.book_id;
      bookCode.value = book.book_code;
      bookTitle.value = book.title;
      bookAuthor.value = book.author;
      bookCategory.value = book.category || "";
      bookCover.value = book.cover_image || "";
      bookStatus.value = book.status;
      bookIsNew.value = book.is_new_arrival;
      bookModal.classList.add("active");
    });
  });

  document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", async () => {
      const ok = confirm("Delete this book?");
      if (!ok) return;

      const res = await fetch("../../api/delete_book.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({ book_id: btn.dataset.id })
      });
      const result = await res.json();
      alert(result.message);
      fetchBooks();
    });
  });
}

function resetForm() {
  bookId.value = "";
  bookCode.value = "";
  bookTitle.value = "";
  bookAuthor.value = "";
  bookCategory.value = "";
  bookCover.value = "";
  bookStatus.value = "available";
  bookIsNew.value = "0";
}

openAddModalBtn.addEventListener("click", () => {
  modalTitle.textContent = "Add Book";
  resetForm();
  bookModal.classList.add("active");
});

closeModalBtn.addEventListener("click", () => {
  bookModal.classList.remove("active");
});

saveBookBtn.addEventListener("click", async () => {
  const payload = {
    book_id: bookId.value,
    book_code: bookCode.value,
    title: bookTitle.value,
    author: bookAuthor.value,
    category: bookCategory.value,
    cover_image: bookCover.value,
    status: bookStatus.value,
    is_new_arrival: Number(bookIsNew.value)
  };

  const endpoint = payload.book_id ? "../../api/update_book.php" : "../../api/add_book.php";

  const res = await fetch(endpoint, {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify(payload)
  });

  const result = await res.json();
  alert(result.message);
  bookModal.classList.remove("active");
  fetchBooks();
});

bookSearch.addEventListener("input", () => {
  renderBooks(bookSearch.value);
});

fetchBooks();