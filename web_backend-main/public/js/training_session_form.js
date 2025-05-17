import { setupInputFilter } from "./input_filter.js";

const nameInput = document.getElementById("name-input"),
      datetimeInput = document.getElementById("datetime-input"),
      durationInput = document.getElementById("duration-input"),
      fileInput = document.getElementById("file-input"),
      form = document.getElementById("form");

const nameFormat = /^[a-zA-Zа-яА-ЯёЁ\s-]+$/;
setupInputFilter(nameInput, nameFormat);

const durationFormat = /^-?\d*(\.\d*)?$/;
setupInputFilter(durationInput, durationFormat);

form.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = nameInput.value,
          datetime = datetimeInput.value,
          duration = durationInput.value;
    if (!name || !datetime || !duration) {
        alert("Заполните обязательные поля ввода");
        return;
    }
    const durationNum = parseFloat(duration);
    if (!nameFormat.test(name) || isNaN(new Date(datetime).getDate()) || isNaN(durationNum)) {
        alert("Введены некорректные данные");
        return;
    }
    if (durationNum <= 0) {
        alert("Длительность должна быть больше 0");
        return;
    }
    const formData = new FormData(form);
    fetch("/training_sessions/add", {
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
    fetch("/training_sessions/upload", {
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