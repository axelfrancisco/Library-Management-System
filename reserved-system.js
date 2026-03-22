const reservedBody = document.getElementById("reservedBody");
const reservedSearch = document.getElementById("reservedSearch");

let allReservedBooks = [];

/* FETCH RESERVED BOOKS */
async function fetchReservedBooks() {
  try {
    const res = await fetch("../../api/get_reserved_books.php");
    const data = await res.json();

    allReservedBooks = data;
    renderReservedBooks();
  } catch (error) {
    console.error("Error fetching reserved books:", error);
  }
}

/* RENDER */
function renderReservedBooks(searchValue = "") {
  if (!reservedBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filtered = allReservedBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.reservedBy.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  reservedBody.innerHTML = "";

  if (filtered.length === 0) {
    reservedBody.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="empty-state">
            <i class="fa-solid fa-bookmark"></i>
            <span>No reserved books yet.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filtered.forEach(book => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.reservedBy}</td>
      <td>${book.reserveDate}</td>
      <td class="reserved">Reserved</td>
    `;

    reservedBody.appendChild(row);
  });
}

/* SEARCH */
if (reservedSearch) {
  reservedSearch.addEventListener("input", () => {
    renderReservedBooks(reservedSearch.value);
  });
}

fetchReservedBooks();