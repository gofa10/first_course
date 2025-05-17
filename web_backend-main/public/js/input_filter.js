export function setupInputFilter(inputElement, format) {
    inputElement.addEventListener("input", (e) => {
        const value = e.target.value;
        if (!format.test(value)) {
            e.target.value = value.slice(0, -1);
        }
    });
}