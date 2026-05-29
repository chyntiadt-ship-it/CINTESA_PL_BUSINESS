const menuToggle = document.getElementById("menuToggle");
const sideNavbar = document.getElementById("sideNavbar");
const closeSidebar = document.getElementById("closeSidebar");

const searchForm = document.getElementById("dashboardSearchForm");
const searchInput = document.getElementById("dashboardSearchInput");
const suggestionBox = document.getElementById("searchSuggestionBox");

const suggestions = [
    "Fashion",
    "Fashion wanita",
    "Fashion pria",
    "Celana cutbray",
    "Celana cutbray wanita",
    "Celana cutbray pria",
    "Celana cutbray jeans",
    "Celana cutbray wanita jumbo",
    "Celana cutbray scuba",
    "Baju wanita",
    "Baju pria",
    "Dress wanita",
    "Sepatu sneakers",
    "Tas wanita",
    "Elektronik",
    "Laptop",
    "Laptop gaming",
    "Handphone Android",
    "Buku",
    "Buku pelajaran",
    "Novel",
    "Furnitur",
    "Furnitur rumah",
    "Meja belajar",
    "Kursi",
    "Barang antik",
    "Perlengkapan bayi"
];

if (menuToggle && sideNavbar) {
    menuToggle.addEventListener("click", function () {
        sideNavbar.classList.add("active");
    });
}

if (closeSidebar && sideNavbar) {
    closeSidebar.addEventListener("click", function () {
        sideNavbar.classList.remove("active");
    });
}

function goToSearch(keyword) {
    const cleanKeyword = keyword.trim();

    if (cleanKeyword !== "") {
        window.location.href = "cari_produk.php?keyword=" + encodeURIComponent(cleanKeyword);
    } else {
        window.location.href = "cari_produk.php";
    }
}

function renderSuggestions(keyword) {
    if (!suggestionBox) return;

    suggestionBox.innerHTML = "";

    const value = keyword.trim().toLowerCase();

    if (value === "") {
        suggestionBox.classList.remove("active");
        return;
    }

    const filtered = suggestions.filter(function (item) {
        return item.toLowerCase().includes(value);
    });

    if (filtered.length === 0) {
        suggestionBox.classList.remove("active");
        return;
    }

    filtered.slice(0, 8).forEach(function (item) {
        const div = document.createElement("div");
        div.className = "search-suggestion-item";

        div.innerHTML = `
            <span>⌕</span>
            <p>${item}</p>
        `;

        div.addEventListener("click", function () {
            goToSearch(item);
        });

        suggestionBox.appendChild(div);
    });

    suggestionBox.classList.add("active");
}

if (searchInput) {
    searchInput.addEventListener("input", function () {
        renderSuggestions(this.value);
    });

    searchInput.addEventListener("focus", function () {
        renderSuggestions(this.value);
    });

    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            goToSearch(this.value);
        }
    });
}

if (searchForm) {
    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (searchInput) {
            goToSearch(searchInput.value);
        }
    });
}

document.addEventListener("click", function (event) {
    if (
        searchForm &&
        suggestionBox &&
        !searchForm.contains(event.target)
    ) {
        suggestionBox.classList.remove("active");
    }
});
