const returnedBody = document.getElementById("returnedBody");
const returnedSearch = document.getElementById("returnedSearch");

let allReturnedBooks = [];

async function fetchReturnedBooks() {
  try {
    const res = await fetch("../../api/get_returned_books.php");
    const data = await res.json();

    allReturnedBooks = data;
    renderReturnedBooks();
  } catch (error) {
    console.error("Error fetching returned books:", error);
  }
}

function renderReturnedBooks(searchValue = "") {
  if (!returnedBody) return;

  const keyword = searchValue.toLowerCase().trim();

  const filteredBooks = allReturnedBooks.filter(book =>
    book.title.toLowerCase().includes(keyword) ||
    book.borrower.toLowerCase().includes(keyword) ||
    book.id.toLowerCase().includes(keyword)
  );

  returnedBody.innerHTML = "";

  if (filteredBooks.length === 0) {
    returnedBody.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="empty-state">
            <i class="fa-solid fa-rotate-left"></i>
            <span>No returned books yet.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  filteredBooks.forEach(book => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.borrower}</td>
      <td>${book.returnDate}</td>
      <td class="returned">Returned</td>
    `;

    returnedBody.appendChild(row);
  });
}

if (returnedSearch) {
  returnedSearch.addEventListener("input", () => {
    renderReturnedBooks(returnedSearch.value);
  });
}

fetchReturnedBooks();