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
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>xx</h5>
                    </div>
                    <div class="card-block">
                        <div id="pie-cost"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>y</h5>
                    </div>
                    <div class="card-block">
                        <div id="line-sales"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>x</h5>
                    </div>
                    <div class="card-block">
                        <div id="bar-returns"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.highcharts.com/highcharts.js"></script>