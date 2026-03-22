seedLibraryData();
seedOverdueData();

const overdueBody = document.getElementById("overdueBody");

function renderOverdueBooks() {
  if (!overdueBody) return;

  const books = getOverdueBooks();

  overdueBody.innerHTML = "";

  if (books.length === 0) {
    overdueBody.innerHTML = `
      <tr>
        <td colspan="6">
          <div class="empty-state">
            <i class="fa-solid fa-clock"></i>
            <span>No overdue books found.</span>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  books.forEach(book => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${book.cover}" alt="${book.title}"></td>
      <td>#${book.id}</td>
      <td>${book.title}</td>
      <td>${book.borrower}</td>
      <td>${book.dueDate}</td>
      <td class="overdue">Overdue (${book.daysLate} days late)</td>
    `;

    overdueBody.appendChild(row);
  });
}

renderOverdueBooks();