/* LOAD DASHBOARD DATA */
apiFetch('/multi_tenant_crm/backend/dashboard/')
    .then(data => {

        /* TOTAL LEADS */
        document.getElementById('totalLeads').innerText =
            data.total_leads || 0;

        /* CONVERSION RATE */
        document.getElementById('conversionRate').innerText =
            (data.conversion_rate || 0) + '%';

        /* LEADS PER STAFF */
        const staffList = document.getElementById('leadsPerStaff');
        staffList.innerHTML = '';

        (data.leads_per_staff || []).forEach(row => {
            let li = document.createElement('li');
            li.textContent = `${row.staff_name} : ${row.total} leads`;
            staffList.appendChild(li);
        });

        /* RECENT ACTIVITY LOGS */
        const activity = document.getElementById('activityList');
        activity.innerHTML = '';

        (data.recent_activity || []).forEach(a => {
            let li = document.createElement('li');
            li.textContent = a.description;
            activity.appendChild(li);
        });

    });

/* LOGOUT */
function logout() {
    localStorage.clear();
    window.location.href = 'login.html';
}
