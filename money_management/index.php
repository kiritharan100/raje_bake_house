<?php
include 'header.php';
 ?>
<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 p-0">
                <div class="main-header d-flex justify-content-between align-items-center">
                    <h4>Dashboard</h4>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Cheque Payable Summary</h5>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dashboard-cheque-summary">
                                <thead>
                                    <tr>
                                        <th style='text-align:center'>Payee</th>
                                        <th width="80" style='text-align:center'>No of Cheque</th>
                                        <th width="100" style='text-align:center'>Today</th>
                                        <th width="100" style='text-align:center'>Within 7 days</th>
                                        <th width="100" style='text-align:center'>Total Payable</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td>Total</td>
                                        <td id="dash-total-count" class="text-center">0</td>
                                        <td id="dash-total-today" class="text-right">0.00</td>
                                        <td id="dash-total-seven" class="text-right">0.00</td>
                                        <td id="dash-total-payable" class="text-right">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5>Total Payable (Pie)</h5>
                    </div>
                    <div class="card-block">
                        <div id="pie-payable"></div>
                    </div>
                </div>
            </div>


        </div>




        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Monthly Credit Sales vs Payment</h5>
                        <div class="d-flex align-items-center">
                            <label class="mr-2 mb-0">Year</label>
                            <select id="monthlyYear" class="form-control form-control-sm" style="width:120px;"></select>
                        </div>
                    </div>
                    <div class="card-block">
                        <div id="credit-monthly-chart"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
function formatAmount(val) {
    const num = parseFloat(val || 0);
    return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function renderPayablePie(data) {
    const seriesData = (data || []).map(r => ({
        name: r.contact_name,
        y: parseFloat(r.total_payable) || 0
    })).filter(item => item.y > 0);

    Highcharts.chart('pie-payable', {
        chart: {
            type: 'pie'
        },
        title: {
            text: ''
        },
        credits: {
            enabled: false
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b> ({point.y:,.2f})'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.percentage:.1f}%'
                }
            }
        },
        series: [{
            name: 'Payable',
            colorByPoint: true,
            data: seriesData
        }]
    });
}

function loadDashboardChequeSummary() {
    $.getJSON('ajax/cheque_summary.php', function(response) {
        if (!response.success) {
            Swal.fire('Error', response.message || 'Unable to load summary.', 'error');
            return;
        }
        const tbody = $('#dashboard-cheque-summary tbody');
        tbody.empty();
        response.data.forEach(function(row) {
            const tr = $('<tr>');
            tr.append(`<td>${row.contact_name}</td>`);
            tr.append(`<td class="text-center">${row.cheque_count}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.today_payable)}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.payable_7_days)}</td>`);
            tr.append(`<td class="text-right">${formatAmount(row.total_payable)}</td>`);
            tbody.append(tr);
        });
        $('#dash-total-count').text(response.total.count);
        $('#dash-total-today').text(formatAmount(response.total.today));
        $('#dash-total-seven').text(formatAmount(response.total.seven_days));
        $('#dash-total-payable').text(formatAmount(response.total.total_payable));
        renderPayablePie(response.data);
    }).fail(function() {
        Swal.fire('Error', 'Failed to load summary.', 'error');
    });
}

function loadMonthlyChart(year) {
    $.getJSON('ajax/credit_sales_monthly.php', {
        year: year
    }, function(resp) {
        if (!resp.success) {
            Swal.fire('Error', resp.message || 'Unable to load monthly data.', 'error');
            return;
        }

        // Populate years dropdown
        const sel = $('#monthlyYear');
        sel.empty();
        (resp.years || []).forEach(function(y) {
            sel.append(`<option value="${y}" ${y == resp.year ? 'selected' : ''}>${y}</option>`);
        });

        const categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        Highcharts.chart('credit-monthly-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Amount'
                }
            },
            tooltip: {
                shared: true,
                valueDecimals: 2,
                valuePrefix: ''
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Credit Sales',
                data: resp.sales || []
            }, {
                name: 'Payments',
                data: resp.payments || []
            }]
        });
    });
}

$(document).ready(function() {
    loadDashboardChequeSummary();
    loadMonthlyChart('');

    $('#monthlyYear').on('change', function() {
        loadMonthlyChart($(this).val());
    });
});
</script>