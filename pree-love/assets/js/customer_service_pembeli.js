const serviceMessage = document.getElementById("serviceMessage");
const charCount = document.getElementById("charCount");

if (serviceMessage && charCount) {
    charCount.textContent = serviceMessage.value.length;

    serviceMessage.addEventListener("input", function () {
        charCount.textContent = this.value.length;
    });
}