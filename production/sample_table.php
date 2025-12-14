 <?php include 'header.php'; ?>

 <?php
// Default date range
if(isset($_REQUEST['from_date'])){
   $from_date = $_REQUEST['from_date'];
   $to_date = $_REQUEST['to_date'];
} else {
   $from_date = date('Y-m-01');
   $to_date = date("Y-m-t");
}

 
?>

 <div class="content-wrapper">
     <div class="container-fluid">

         <div class="row">
             <div class="col-sm-12 p-0">
                 <div class="main-header">
                     <h4>Sample Table</h4>
                 </div>
             </div>
         </div>

         <div class="row">
             <div class="col-sm-12">
                 <div class="card">

                     <div class="card-header" align='right'>
                         <form method="GET">

                             <!-- DATE FILTER -->
                             From
                             <input type="date" name="from_date" value="<?php echo $from_date; ?>">
                             To
                             <input type="date" name="to_date" value="<?php echo $to_date; ?>">




                             <button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>
                             <button type="button" id="exportButton"
                                 filename="<?php echo 'USER LOG_'.$client_name; ?>.xlsx" class="btn btn-primary"><i
                                     class="ti-cloud-down"></i> Export</button>
                         </form>
                     </div>


                     <div class="card-block">
                         <div class="row">
                             <div class="col-sm-12 table-responsive">

                                 <table id="example" class="table table-bordered table-striped" style="width:100%;">
                                     <thead>
                                         <tr>
                                             <th width="20px">#</th>
                                             <th width="100px">User</th>


                                             <th width="70px">Activity</th>
                                             <th>Detail</th>
                                             <th width="120px">Location</th>
                                             <th width="60px">Date</th>
                                         </tr>
                                     </thead>

                                     <tbody>


                                     </tbody>

                                 </table>

                             </div>
                         </div>
                     </div>

                 </div>
             </div>
         </div>

     </div>

     <?php include 'footer.php'; ?>