document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('managersModal');
    const modalContentEl = document.getElementById('managersModalContent');

    document.getElementById('loadManagersModal').addEventListener('click', async (e) => {
        const feedbackId = e.target.getAttribute('data-id');
        if (!feedbackId) return;

        try {
            const response = await fetch(`/admin/feedbacks/${feedbackId}/editors`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) throw new Error('Ошибка загрузки формы');

            modalContentEl.innerHTML = await response.text();

            const selectEl = modalContentEl.querySelector('#managers');
            if (selectEl) {
                new Choices(selectEl, {
                    removeItemButton: true,
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                });
            }

            const form = modalContentEl.querySelector('#managersForm');
            if (form) {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const formData = new FormData(form);
                    try {
                        const saveResponse = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-Token': window.csrfToken
                            },
                        });

                        if (!saveResponse.ok) throw new Error('Ошибка сохранения');

                        const data = await saveResponse.json();

                        if (data.success) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            modalInstance.hide();

                            const listEl = document.getElementById('managersList');
                            if (listEl && Array.isArray(data.editors)) {
                                listEl.innerHTML = '';
                                data.editors.forEach(editor => {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item';
                                    li.textContent = editor.name;
                                    listEl.appendChild(li);
                                });
                            }
                        } else {
                            alert(data.message || 'Ошибка при сохранении');
                        }
                    } catch (err) {
                        alert('Ошибка при сохранении. Попробуйте позже.');
                        console.error(err);
                    }
                });
            }
        } catch (error) {
            alert('Ошибка загрузки модального окна. Попробуйте позже.');
            console.error(error);
        }
    });
});