const themePicker = document.querySelector('[data-theme-picker]');

function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    if (themePicker) {
        themePicker.value = theme;
    }
}

applyTheme(localStorage.getItem('spendsense-theme') === 'light' ? 'light' : 'dark');
themePicker?.addEventListener('change', () => {
    localStorage.setItem('spendsense-theme', themePicker.value);
    applyTheme(themePicker.value);
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

const transactionForm = document.querySelector('[data-transaction-form]');
if (transactionForm) {
    const categorySelect = transactionForm.elements.category_id;
    const optionGroups = Array.from(categorySelect.querySelectorAll('optgroup'));

    function filterCategories() {
        const selectedCategory = categorySelect.value;
        const type = transactionForm.elements.type.value;
        optionGroups.forEach((group) => group.remove());
        optionGroups.filter((group) => group.dataset.type === type)
            .forEach((group) => categorySelect.append(group));
        categorySelect.value = Array.from(categorySelect.options).some((option) => option.value === selectedCategory)
            ? selectedCategory : '';
    }

    transactionForm.querySelectorAll('input[name="type"]').forEach((radio) => {
        radio.addEventListener('change', filterCategories);
    });
    filterCategories();
}
