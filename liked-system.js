const likedBody = document.getElementById("likedBody");
const likedSearch = document.getElementById("likedSearch");

let allLikedBooks = [];
let borrowedBookIds = [];

async function fetchLikedBooks() {
  try {
    const [likedRes, borrowedRes] = await Promise.all([
      fetch("../../api/get_liked_books.php"),
      fetch("../../api/get_borrowed_books.php")
    ]);

    const likedData = await likedRes.json();
    const borrowedData = await borrowedRes.json();

    allLikedBooks = likedData;
    borrowedBookIds = borrowedData.map(book => book.id);

    renderLikedBooks();
  } catch (error) {
    console.error("Error fetching liked books:", error);
  }
}

function updateLikedCount(filteredCount = null) {
  const count = filteredCount !== null ? filteredCount : allLikedBooks.length;
  const el = document.getElementById("likedCount");

  if (el) el.textContent = count;
}

async function removeLiked(bookId) {
  try {
    const response = await fetch("../../api/remove_liked.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        book_id: bookId
      })
    });

    const result = await response.json();

    if (result.success) {
      await fetchLikedBooks();
      alert(result.message);
    } else {
      alert(result.message || "Failed to remove liked book.");
    }
  } catch (error) {
    console.error("Remove liked error:", error);
    alert("Something went wrong while removing liked book.");
  }
}

async function borrowBook(bookId) {
  try {
    const response = await fetch("../../api/borrow_book.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        book_id: bookId
      })
    });

    const result = await response.json();

    if (result.success) {
      await fetchLikedBooks();
      alert(result.message);
    } else {
      alert(result.message || "Failed to borrow book.");
    }
  } catch (error) {
    console.error("Borrow from liked error:", error);
    alert("Something went wrong while borrowing the book.");
  }
}

function renderLikedBooks(searchValue = "") {
  if (!likedBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredBooks = allLikedBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.author.toLowerCase().includes(keyword) ||
    book.category.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  likedBody.innerHTML = "";

  if (filteredBooks.length === 0) {
    likedBody.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="empty-state">
            <i class="fa-solid fa-heart"></i>
            <span>No liked books yet.</span>
          </div>
        </td>
      </tr>
    `;
    updateLikedCount(0);
    return;
  }

  filteredBooks.forEach(book => {
    const isBorrowed = borrowedBookIds.includes(book.id);

    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.author}</td>
      <td>${book.category}</td>
      <td>
        <div class="action-cell">
          <button class="borrow-btn ${isBorrowed ? "borrowed" : ""}" data-id="${book.id}" ${isBorrowed ? "disabled" : ""}>
            ${isBorrowed ? "Borrowed" : "Borrow"}
          </button>
          <button class="remove-btn" data-id="${book.id}">Remove</button>
        </div>
      </td>
    `;

    likedBody.appendChild(row);
  });

  document.querySelectorAll(".borrow-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      if (btn.disabled) return;

      const confirmBorrow = confirm("Are you sure you want to borrow this book?");
      if (!confirmBorrow) return;

      borrowBook(btn.dataset.id);
    });
  });

  document.querySelectorAll(".remove-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const confirmRemove = confirm("Are you sure you want to remove this liked book?");
      if (!confirmRemove) return;

      removeLiked(btn.dataset.id);
    });
  });

  updateLikedCount(filteredBooks.length);
}

if (likedSearch) {
  likedSearch.addEventListener("input", () => {
    renderLikedBooks(likedSearch.value);
  });
}

fetchLikedBooks();