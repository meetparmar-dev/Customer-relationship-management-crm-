let projectChart, taskChart;

// INIT charts
function initCharts(projectLabels, projectData, taskData) {

    if (projectChart) projectChart.destroy();
    if (taskChart) taskChart.destroy();

    projectChart = new Chart(document.getElementById('projectChart'), {
        type: 'line',
        data: {
            labels: projectLabels,
            datasets: [{
                data: projectData,
                borderColor: '#0d6efd',
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } }
        }
    });

    taskChart = new Chart(document.getElementById('taskChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [
                    taskData.pending,
                    taskData.in_progress,
                    taskData.completed
                ],
                backgroundColor: ['#ffc107', '#0d6efd', '#198754']
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

// INITIAL LOAD
initCharts(
    window.DASHBOARD.projectLabels,
    window.DASHBOARD.projectData,
    window.DASHBOARD.taskData
);

// DROPDOWN CHANGE
document.getElementById('periodSelect')
    .addEventListener('change', function () {

        fetch(`${window.DASHBOARD.statsUrl}?period=${this.value}`)
            .then(res => res.json())
            .then(data => {

                // Cards
                document.getElementById('card-clients').textContent   = data.cards.clients;
                document.getElementById('card-projects').textContent  = data.cards.projects;
                document.getElementById('card-tasks').textContent     = data.cards.tasks;
                document.getElementById('card-pending').textContent   = data.cards.pending;
                document.getElementById('card-completed').textContent = data.cards.completed;

                // Charts
                initCharts(
                    data.projects.labels,
                    data.projects.data,
                    data.tasks
                );
            });
    });
