const newArrivalBody = document.getElementById("newArrivalBody");
const newArrivalSearch = document.getElementById("newArrivalSearch");

let allNewArrivalBooks = [];
let reservedBookIds = [];

async function fetchNewArrivalBooks() {
  try {
    const [newArrivalRes, reservedRes] = await Promise.all([
      fetch("../../api/get_new_arrival_books.php"),
      fetch("../../api/get_reserved_books.php")
    ]);

    const newArrivalData = await newArrivalRes.json();
    const reservedData = await reservedRes.json();

    allNewArrivalBooks = newArrivalData;
    reservedBookIds = reservedData.map(book => book.id);

    renderNewArrivalBooks();
  } catch (error) {
    console.error("Error fetching new arrival books:", error);
  }
}

async function reserveBook(bookId) {
  try {
    const res = await fetch("../../api/reserve_book.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        book_id: bookId
      })
    });

    const result = await res.json();

    if (result.success) {
      alert(result.message);
      await fetchNewArrivalBooks();
    } else {
      alert(result.message || "Failed to reserve book.");
    }
  } catch (err) {
    console.error("Reserve error:", err);
    alert("Something went wrong while reserving the book.");
  }
}

function renderNewArrivalBooks(searchValue = "") {
  if (!newArrivalBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredBooks = allNewArrivalBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.author.toLowerCase().includes(keyword) ||
    book.category.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  newArrivalBody.innerHTML = "";

  if (filteredBooks.length === 0) {
    newArrivalBody.innerHTML = `
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fa-solid fa-star"></i>
            <span>No new arrival books found.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filteredBooks.forEach(book => {
    const isReserved = reservedBookIds.includes(book.id);

    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.author}</td>
      <td>${book.category || "General"}</td>
      <td class="newarrival">New Arrival</td>
      <td>
        <button 
          class="reserve-btn ${isReserved ? "reserved" : ""}" 
          data-id="${book.id}" 
          ${isReserved ? "disabled" : ""}
        >
          ${isReserved ? "Reserved" : "Reserve"}
        </button>
      </td>
    `;

    newArrivalBody.appendChild(row);
  });

  document.querySelectorAll(".reserve-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      if (btn.disabled) return;

      const confirmReserve = confirm("Reserve this book?");
      if (!confirmReserve) return;

      reserveBook(btn.dataset.id);
    });
  });
}

if (newArrivalSearch) {
  newArrivalSearch.addEventListener("input", () => {
    renderNewArrivalBooks(newArrivalSearch.value);
  });
}

fetchNewArrivalBooks();