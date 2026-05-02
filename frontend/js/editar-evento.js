const editModal = document.getElementById('editEventModal');
const closeEditBtn = document.getElementById('closeEditModal');
const cancelEditBtn = document.getElementById('btnCancelEdit');

function openEditModal() {
    editModal.classList.add('active');
}

const closeEdit = () => editModal.classList.remove('active');

closeEditBtn.addEventListener('click', closeEdit);
cancelEditBtn.addEventListener('click', closeEdit);

editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeEdit();
});

document.getElementById('editEventForm').addEventListener('submit', (e) => {
    e.preventDefault();
    console.log("Cambios guardados con éxito");
    closeEdit();
});

openEditModal();