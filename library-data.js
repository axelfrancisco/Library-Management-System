const PROFILE_KEY = "library_user_profile";

const LIBRARY_KEYS = {
  available: "library_available_books",
  liked: "library_liked_books",
  borrowed: "library_borrowed_books",
  returned: "library_returned_books",
  reserved: "library_reserved_books",
  newArrival: "library_new_arrival_books",
  seeded: "library_seeded_once"
};

function getToday() {
  const date = new Date();
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric"
  });
}

function getCurrentUserName() {
  const savedProfile = JSON.parse(localStorage.getItem(PROFILE_KEY));

  if (savedProfile && savedProfile.name && savedProfile.name.trim() !== "") {
    return savedProfile.name.trim();
  }

  return "You";
}

function seedLibraryData() {
  if (localStorage.getItem(LIBRARY_KEYS.seeded)) return;

  const availableBooks = [
    {
      id: "3011",
      cover: "https://covers.openlibrary.org/b/id/10521270-L.jpg",
      title: "Atomic Habits",
      author: "James Clear",
      category: "Self Help"
    },
    {
      id: "3012",
      cover: "https://covers.openlibrary.org/b/id/8231996-L.jpg",
      title: "The Alchemist",
      author: "Paulo Coelho",
      category: "Fiction"
    },
    {
      id: "3013",
      cover: "https://covers.openlibrary.org/b/id/6979861-L.jpg",
      title: "The Hobbit",
      author: "J.R.R Tolkien",
      category: "Fantasy"
    },
    {
      id: "3014",
      cover: "https://covers.openlibrary.org/b/id/8091016-L.jpg",
      title: "Pride and Prejudice",
      author: "Jane Austen",
      category: "Romance"
    },
    {
      id: "3015",
      cover: "https://covers.openlibrary.org/b/id/7222246-L.jpg",
      title: "1984",
      author: "George Orwell",
      category: "Dystopian"
    }
  ];

  const newArrivalBooks = [
    {
      id: "6011",
      cover: "https://covers.openlibrary.org/b/id/11153254-L.jpg",
      title: "It Ends With Us",
      author: "Colleen Hoover",
      arrivalDate: "March 20, 2026"
    },
    {
      id: "6012",
      cover: "https://covers.openlibrary.org/b/id/10594765-L.jpg",
      title: "The Midnight Library",
      author: "Matt Haig",
      arrivalDate: "March 19, 2026"
    },
    {
      id: "6013",
      cover: "https://covers.openlibrary.org/b/id/12611055-L.jpg",
      title: "Fourth Wing",
      author: "Rebecca Yarros",
      arrivalDate: "March 18, 2026"
    },
    {
      id: "6014",
      cover: "https://covers.openlibrary.org/b/id/12885872-L.jpg",
      title: "Iron Flame",
      author: "Rebecca Yarros",
      arrivalDate: "March 17, 2026"
    }
  ];

  localStorage.setItem(LIBRARY_KEYS.available, JSON.stringify(availableBooks));
  localStorage.setItem(LIBRARY_KEYS.newArrival, JSON.stringify(newArrivalBooks));
  localStorage.setItem(LIBRARY_KEYS.liked, JSON.stringify([]));
  localStorage.setItem(LIBRARY_KEYS.borrowed, JSON.stringify([]));
  localStorage.setItem(LIBRARY_KEYS.returned, JSON.stringify([]));
  localStorage.setItem(LIBRARY_KEYS.reserved, JSON.stringify([]));
  localStorage.setItem(LIBRARY_KEYS.seeded, "true");
}

function getBooks(key) {
  return JSON.parse(localStorage.getItem(key)) || [];
}

function setBooks(key, books) {
  localStorage.setItem(key, JSON.stringify(books));
}

function existsById(list, id) {
  return list.some(book => book.id === id);
}

function addToLiked(book) {
  const liked = getBooks(LIBRARY_KEYS.liked);

  if (!existsById(liked, book.id)) {
    liked.push({
      id: book.id,
      cover: book.cover,
      title: book.title,
      author: book.author,
      category: book.category
    });

    setBooks(LIBRARY_KEYS.liked, liked);
  }
}

function removeLikedBook(id) {
  const liked = getBooks(LIBRARY_KEYS.liked).filter(book => book.id !== id);
  setBooks(LIBRARY_KEYS.liked, liked);
}

function borrowFromAvailable(id) {
  const available = getBooks(LIBRARY_KEYS.available);
  const borrowed = getBooks(LIBRARY_KEYS.borrowed);

  const index = available.findIndex(book => book.id === id);
  if (index === -1) return;

  const book = available[index];

  if (!existsById(borrowed, id)) {
    borrowed.push({
      id: book.id,
      cover: book.cover,
      title: book.title,
      author: book.author,
      category: book.category,
      borrower: getCurrentUserName(),
      borrowDate: getToday()
    });

    setBooks(LIBRARY_KEYS.borrowed, borrowed);
  }

  available.splice(index, 1);
  setBooks(LIBRARY_KEYS.available, available);
}

function borrowFromLiked(id) {
  const liked = getBooks(LIBRARY_KEYS.liked);
  const borrowed = getBooks(LIBRARY_KEYS.borrowed);

  const book = liked.find(item => item.id === id);
  if (!book) return;

  if (!existsById(borrowed, id)) {
    borrowed.push({
      id: book.id,
      cover: book.cover,
      title: book.title,
      author: book.author,
      category: book.category,
      borrower: getCurrentUserName(),
      borrowDate: getToday()
    });

    setBooks(LIBRARY_KEYS.borrowed, borrowed);
  }
}

function returnBorrowedBook(id) {
  const borrowed = getBooks(LIBRARY_KEYS.borrowed);
  const returned = getBooks(LIBRARY_KEYS.returned);
  const available = getBooks(LIBRARY_KEYS.available);

  const index = borrowed.findIndex(book => book.id === id);
  if (index === -1) return;

  const book = borrowed[index];

  returned.push({
    id: book.id,
    cover: book.cover,
    title: book.title,
    borrower: book.borrower,
    returnDate: getToday()
  });

  if (!existsById(available, id)) {
    available.push({
      id: book.id,
      cover: book.cover,
      title: book.title,
      author: book.author,
      category: book.category
    });
  }

  borrowed.splice(index, 1);

  setBooks(LIBRARY_KEYS.borrowed, borrowed);
  setBooks(LIBRARY_KEYS.returned, returned);
  setBooks(LIBRARY_KEYS.available, available);
}

function reserveFromNewArrival(id) {
  const newArrival = getBooks(LIBRARY_KEYS.newArrival);
  const reserved = getBooks(LIBRARY_KEYS.reserved);

  const book = newArrival.find(item => item.id === id);
  if (!book) return;

  if (!existsById(reserved, id)) {
    reserved.push({
      id: book.id,
      cover: book.cover,
      title: book.title,
      author: book.author,
      reservedBy: getCurrentUserName(),
      reservedDate: getToday()
    });

    setBooks(LIBRARY_KEYS.reserved, reserved);
  }
}

/* OVERDUE + FINE SYSTEM */

function seedOverdueData() {
  const overdueKey = "library_overdue_books";

  if (!localStorage.getItem(overdueKey)) {
    const overdueBooks = [
      {
        id: "4011",
        cover: "https://covers.openlibrary.org/b/id/6979861-L.jpg",
        title: "The Hobbit",
        borrower: "Juan Dela Cruz",
        dueDate: "March 10, 2026",
        daysLate: 5
      },
      {
        id: "4012",
        cover: "https://covers.openlibrary.org/b/id/8231856-L.jpg",
        title: "To Kill a Mockingbird",
        borrower: "Maria Santos",
        dueDate: "March 9, 2026",
        daysLate: 6
      },
      {
        id: "4013",
        cover: "https://covers.openlibrary.org/b/id/7222246-L.jpg",
        title: "1984",
        borrower: "Carlos Reyes",
        dueDate: "March 8, 2026",
        daysLate: 7
      },
      {
        id: "4014",
        cover: "https://covers.openlibrary.org/b/id/8091016-L.jpg",
        title: "Pride and Prejudice",
        borrower: "Ana Lopez",
        dueDate: "March 7, 2026",
        daysLate: 8
      }
    ];

    localStorage.setItem(overdueKey, JSON.stringify(overdueBooks));
  }

  syncFinesFromOverdue();
}

function getOverdueBooks() {
  return JSON.parse(localStorage.getItem("library_overdue_books")) || [];
}

function setOverdueBooks(books) {
  localStorage.setItem("library_overdue_books", JSON.stringify(books));
}

function getFines() {
  return JSON.parse(localStorage.getItem("library_fines")) || [];
}

function setFines(fines) {
  localStorage.setItem("library_fines", JSON.stringify(fines));
}

function calculateFine(daysLate) {
  return daysLate * 10;
}

function syncFinesFromOverdue() {
  const overdueBooks = getOverdueBooks();
  const currentFines = getFines();

  const updatedFines = overdueBooks.map(book => {
    const existingFine = currentFines.find(fine => fine.id === book.id);

    return {
      id: book.id,
      cover: book.cover,
      title: book.title,
      borrower: book.borrower,
      dueDate: book.dueDate,
      daysLate: book.daysLate,
      amount: calculateFine(book.daysLate),
      status: existingFine ? existingFine.status : "Unpaid"
    };
  });

  setFines(updatedFines);
}

function payFine(id) {
  const fines = getFines();

  const updatedFines = fines.map(fine => {
    if (fine.id === id) {
      return {
        ...fine,
        status: "Paid"
      };
    }
    return fine;
  });

  setFines(updatedFines);
}