const availableBody = document.getElementById("availableBody");
const availableSearch = document.getElementById("availableSearch");

let allBooks = [];
let likedBookIds = [];

async function fetchBooks() {
  try {
    const [booksRes, likedRes] = await Promise.all([
      fetch("../../api/get_books.php"),
      fetch("../../api/get_liked_books.php")
    ]);

    const booksData = await booksRes.json();
    const likedData = await likedRes.json();

    allBooks = booksData;
    likedBookIds = likedData.map(book => book.id);

    renderAvailableBooks();
  } catch (error) {
    console.error("Error fetching books:", error);
  }
}

function updateAvailableCount(filteredCount = null) {
  const count = filteredCount !== null ? filteredCount : allBooks.length;
  const countEl = document.getElementById("availableCount");

  if (countEl) {
    countEl.textContent = count;
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
      await fetchBooks();
      alert(result.message);
    } else {
      alert(result.message || "Failed to borrow book.");
    }
  } catch (error) {
    console.error("Borrow error:", error);
    alert("Something went wrong while borrowing the book.");
  }
}

async function addLiked(bookId) {
  try {
    const response = await fetch("../../api/add_liked.php", {
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
      await fetchBooks();
    } else {
      alert(result.message || "Failed to like book.");
    }
  } catch (error) {
    console.error("Like error:", error);
    alert("Something went wrong while liking the book.");
  }
}

function renderAvailableBooks(searchValue = "") {
  if (!availableBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredBooks = allBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.author.toLowerCase().includes(keyword) ||
    book.category.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  availableBody.innerHTML = "";

  if (filteredBooks.length === 0) {
    availableBody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fa-solid fa-book"></i>
            <span>No available books found.</span>
          </div>
        </td>
      </tr>
    `;
    updateAvailableCount(0);
    return;
  }

  filteredBooks.forEach(book => {
    const isLiked = likedBookIds.includes(book.id);

    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.author}</td>
      <td>${book.category}</td>
      <td class="available">Available</td>
      <td>
        <div class="action-cell">
          <button class="borrow-btn" data-id="${book.id}">Borrow</button>
          <button class="like-btn ${isLiked ? "liked" : ""}" data-id="${book.id}" ${isLiked ? "disabled" : ""}>
            <i class="fa-solid fa-heart"></i>
          </button>
        </div>
      </td>
    `;

    availableBody.appendChild(row);
  });

  document.querySelectorAll(".borrow-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const confirmBorrow = confirm("Are you sure you want to borrow this book?");
      if (!confirmBorrow) return;

      borrowBook(btn.dataset.id);
    });
  });

  document.querySelectorAll(".like-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      addLiked(btn.dataset.id);
    });
  });

  updateAvailableCount(filteredBooks.length);
}

if (availableSearch) {
  availableSearch.addEventListener("input", () => {
    renderAvailableBooks(availableSearch.value);
  });
}

fetchBooks();