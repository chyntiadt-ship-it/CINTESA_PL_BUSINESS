const accordion = document.querySelector(".accordion");
const accordionHeader = document.querySelector(".accordion-header");

if (accordion && accordionHeader) {
    accordionHeader.addEventListener("click", function () {
        accordion.classList.toggle("active");
    });
}