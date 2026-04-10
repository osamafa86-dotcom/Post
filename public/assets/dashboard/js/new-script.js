// رسم الدائرة (تفصيل الزيارات)
var ctx1 = document.getElementById('visitDetailsChart');
if(ctx1){
    ctx1.getContext('2d');

    var visitDetailsChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['الرئيسية', 'صفحات أخرى'],
            datasets: [{
                label: 'تفصيل الزيارات',
                data: [30, 70],
                backgroundColor: ['#007bff', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });

// رسم الزيارات اليومية
    var ctx2 = document.getElementById('dailyVisitsChart').getContext('2d');
    var dailyVisitsChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['اليوم 1', 'اليوم 2', 'اليوم 3', 'اليوم 4', 'اليوم 5', 'اليوم 6', 'اليوم 7'],
            datasets: [{
                label: 'الزيارات اليومية',
                data: [12, 19, 3, 5, 2, 3, 7],
                backgroundColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });

}

