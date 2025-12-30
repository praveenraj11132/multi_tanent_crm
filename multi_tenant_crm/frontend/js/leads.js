let page = 1;
const leadsList = document.getElementById('leads');
const createBtn = document.getElementById('createBtn');

const user = getUser();

/* ROLE BASED UI */
if (user.role === 'staff') {
    createBtn.style.display = 'none';
}

/* LOAD LEADS */
function loadLeads() {
    apiFetch(`/multi_tenant_crm/backend/leads/?page=${page}&limit=5`)
        .then(res => {
            leadsList.innerHTML = '';
            res.data.forEach(l => {
                let li = document.createElement('li');
                li.textContent = `${l.full_name} - ${l.status}`;
                leadsList.appendChild(li);
            });
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('leadForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            createLead();
        });
    }

    loadLeads();
});

/* CREATE LEAD */
function createLead() {
    apiFetch('/multi_tenant_crm/backend/leads/create.php', {
        method: 'POST',
        body: JSON.stringify({
            full_name: document.getElementById('full_name').value,
            email: document.getElementById('email').value
        })
    }).then(res => {
        if (res.error) {
            alert(res.error);
            return;
        }
        alert(res.message);
        loadLeads();
    });
}

/* PAGINATION */
function next() {
    page++;
    loadLeads();
}

function prev() {
    if (page > 1) page--;
    loadLeads();
}

loadLeads();
