<script type="text/javascript" src="{{ asset('admin/chart_js/chart.min.js') }}"></script> 

<script>

var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            '{{trans("dashboard.week_days.mon")}}',
            '{{trans("dashboard.week_days.tue")}}', 
            '{{trans("dashboard.week_days.wed")}}',
            '{{trans("dashboard.week_days.thu")}}',
            '{{trans("dashboard.week_days.fri")}}',
            '{{trans("dashboard.week_days.sat")}}',
            '{{trans("dashboard.week_days.sun")}}',
        ],
        datasets: [{
            label: "{{trans('dashboard.customers')}}",
            data: [],
            backgroundColor: '#dd4b39',
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>

