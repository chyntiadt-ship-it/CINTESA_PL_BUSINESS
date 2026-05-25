const searchInput = document.querySelector(".input-box");

if(searchInput){

    searchInput.addEventListener("focus", () => {

        searchInput.parentElement.style.transform = "scale(1.01)";

    });

    searchInput.addEventListener("blur", () => {

        searchInput.parentElement.style.transform = "scale(1)";

    });

}