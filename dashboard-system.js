seedLibraryData();
seedOverdueData();
syncFinesFromOverdue();

function updateDashboardCounts() {
  const availableCount = document.getElementById("availableCount");
  const borrowedCount = document.getElementById("borrowedCount");
  const returnedCount = document.getElementById("returnedCount");
  const overdueCount = document.getElementById("overdueCount");
  const reservedCount = document.getElementById("reservedCount");
  const newArrivalCount = document.getElementById("newArrivalCount");

  if (availableCount) {
    availableCount.textContent = getBooks(LIBRARY_KEYS.available).length;
  }

  if (borrowedCount) {
    borrowedCount.textContent = getBooks(LIBRARY_KEYS.borrowed).length;
  }

  if (returnedCount) {
    returnedCount.textContent = getBooks(LIBRARY_KEYS.returned).length;
  }

  if (reservedCount) {
    reservedCount.textContent = getBooks(LIBRARY_KEYS.reserved).length;
  }

  if (newArrivalCount) {
    newArrivalCount.textContent = getBooks(LIBRARY_KEYS.newArrival).length;
  }

  if (overdueCount) {
    overdueCount.textContent = getOverdueBooks().length;
  }
}

updateDashboardCounts();