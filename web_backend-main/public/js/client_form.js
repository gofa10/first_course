import { setupInputFilter } from "./input_filter.js";

const nameInput = document.getElementById("name-input"),
      phoneInput = document.getElementById("phone-input"),
      birthDateInput = document.getElementById("birthdate-input"),
      fileInput = document.getElementById("file-input"),
      form = document.getElementById("form");

const nameFormat = /^[a-zA-Zа-яА-ЯёЁ\s]*(?:-[a-zA-Zа-яА-ЯёЁ\s]*)?$/;
setupInputFilter(nameInput, nameFormat);

const maskOptions = {
    mask: '+{7}(000)000-00-00'
};
const mask = IMask(phoneInput, maskOptions);

form.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = nameInput.value,
          phone = phoneInput.value,
          birthDate = birthDateInput.value;
    if (!name || !phone) {
        alert("Заполните обязательные поля ввода");
        return;
    }
    const phoneFormat = /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/;
    const isDateInvalid = birthDate ? isNaN(new Date(birthDate).getDate()) : false;
    if (!nameFormat.test(name) || !phoneFormat.test(phone) || isDateInvalid) {
        alert("Введены некорректные данные");
        return;
    }
    const formData = new FormData(form);
    fetch("/clients/add", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        form.reset();
        if (data.message) {
            alert(data.message);
        }
    });
});

fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    if (!file) {
        alert("Файл не выбран");
        return;
    }
    if (!file.name.endsWith(".csv")) {
        alert("Выберите CSV-файл");
        return;
    }
    const formData = new FormData();
    formData.append("file", file);
    fetch("/clients/upload", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        }
    });
});