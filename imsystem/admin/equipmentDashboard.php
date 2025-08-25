<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<?php include 'includes/session.php'; ?>
<?php 
  include 'includes/timezone.php'; 
  $today = date('Y-m-d');
  $year = date('Y');
  if(isset($_GET['year'])){
    $year = $_GET['year'];
  }
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
      </h1>
     
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']." 
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']." 
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-xs-6 rounded-box">
          <!-- small box -->
          <a href="book.php">
            <div class="small-box bg-aqua stat-box">
              <div class="inner">
                <?php
                  $sql = "SELECT * FROM books";
                  $query = $conn->query($sql);
                  echo "<h3>".$query->num_rows."</h3>";
                ?>
                <p>Total Equipments <i class="fa fa-arrow-circle-right"></i></p>
              </div>
              <div class="icon">
                <i class="fa fa-wrench"></i>
              </div>
            </div>
          </a>
        </div>
      
        <div class="col-lg-3 col-xs-6 rounded-box">
          <!-- small box -->
          <a href="return.php">
            <div class="small-box bg-yellow stat-box">
              <div class="inner">
                <?php
                  $sql = "SELECT * FROM returns WHERE date_return = '$today'";
                  $query = $conn->query($sql);
                  echo "<h3>".$query->num_rows."</h3>";
                ?>
                <p>Returned Today <i class="fa fa-arrow-circle-right"></i></p>
              </div>
              <div class="icon">
                <i class="fa fa-mail-reply"></i>
              </div>
            </div>
          </a>
        </div>
        
        <div class="col-lg-3 col-xs-6 rounded-box">
          <!-- small box -->
          <a href="borrow.php">
            <div class="small-box bg-red stat-box">
              <div class="inner">
                <?php
                  $sql = "SELECT * FROM borrow WHERE date_borrow = '$today'";
                  $query = $conn->query($sql);
                  echo "<h3>".$query->num_rows."</h3>";
                ?>
                <p>Borrowed Today <i class="fa fa-arrow-circle-right"></i></p>
              </div>
              <div class="icon">
                <i class="fa fa-mail-forward"></i>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Monthly Transaction Report -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box stat-box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Monthly Transaction Report</h3>
              <div class="box-tools pull-right">
                <form class="form-inline">
                  <div class="form-group">
                    <label>Select Year: </label>
                    <select class="form-control input-sm" id="select_year">
                      <?php
                        for($i=2015; $i<=2265; $i++){
                          $selected = ($i==$year)?'selected':''; 
                          echo "
                            <option value='".$i."' ".$selected.">".$i."</option>
                          ";
                        }
                      ?>
                    </select>
                  </div>
                </form>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <br>
                <div id="legend" class="text-center"></div>
                <canvas id="barChart" style="height:350px"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>
  </div>
</div>

<style>
/* Styling for the Monthly Transaction Report box */
.floating-box {
  border-radius: 12px; /* Rounded corners */
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1); /* Floating effect */
  background: #fff; /* Ensure the background is white */
  transition: transform 0.25s ease-out, box-shadow 0.25s ease-out; /* Smooth hover effect */
  padding: 20px; /* Add padding for spacing inside the box */
}

.stat-box .inner h3, 
.stat-box .inner p {
  color: white !important; /* Ensures text remains white */
}

/* Smoother hover transition for small-box */
.small-box {
  border-radius: 12px;
  padding: 20px;
  transition: transform 0.25s ease-out, box-shadow 0.25s ease-out; /* Adjusted transition */
  position: relative; /* Ensure proper positioning context */
}

/* Fix icon positioning to prevent overlap */
.small-box .icon {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 1;
  opacity: 0.3;
  color: #ffffff;
  pointer-events: none; /* Prevent icon from interfering with clicks */
}

.small-box .icon i {
  font-size: 70px;
}

/* Ensure inner content has proper spacing and positioning */
.small-box .inner {
  position: relative;
  z-index: 2;
  padding-right: 80px; /* Add padding to prevent text overlap with icon */
}

.small-box .inner h3 {
  font-size: 38px;
  font-weight: bold;
  margin: 0 0 10px 0;
  white-space: nowrap;
  padding: 0;
}

.small-box .inner p {
  font-size: 15px;
  margin: 0;
  white-space: nowrap;
}

/* Ensure the anchor tag covers the entire box properly */
.small-box a {
  display: block;
  color: inherit;
  text-decoration: none;
}

.small-box:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

@media screen and (max-width: 768px) {
  .content-wrapper {
    padding: 10px;
  }
  
  .box {
    overflow-x: auto;
    white-space: nowrap;
  }
  
  canvas {
    width: 100% !important;
    height: auto !important;
  }
  
  /* Adjust for mobile */
  .small-box .inner {
    padding-right: 60px;
  }
  
  .small-box .icon i {
    font-size: 50px;
  }
}
</style>

<!-- Chart Data -->
<?php
  $and = 'AND YEAR(date) = '.$year;
  $months = array();
  $return = array();
  $borrow = array();
  for( $m = 1; $m <= 12; $m++ ) {
    $sql = "SELECT * FROM returns WHERE MONTH(date_return) = '$m' AND YEAR(date_return) = '$year'";
    $rquery = $conn->query($sql);
    array_push($return, $rquery->num_rows);

    $sql = "SELECT * FROM borrow WHERE MONTH(date_borrow) = '$m' AND YEAR(date_borrow) = '$year'";
    $bquery = $conn->query($sql);
    array_push($borrow, $bquery->num_rows);

    $num = str_pad( $m, 2, 0, STR_PAD_LEFT );
    $month =  date('M', mktime(0, 0, 0, $m, 1));
    array_push($months, $month);
  }
  
  $months = json_encode($months);
  $return = json_encode($return);
  $borrow = json_encode($borrow);
?>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  var barChartCanvas = $('#barChart').get(0).getContext('2d')
  var barChart = new Chart(barChartCanvas)
  var barChartData = {
    labels  : <?php echo $months; ?>,
    datasets: [
      {
        label               : 'Borrow',
        fillColor           : 'rgba(210, 214, 222, 1)',
        strokeColor         : 'rgba(210, 214, 222, 1)',
        pointColor          : 'rgba(210, 214, 222, 1)',
        pointStrokeColor    : '#c1c7d1',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(220,220,220,1)',
        data                : <?php echo $borrow; ?>
      },
      {
        label               : 'Return',
        fillColor           : 'rgba(60,141,188,0.9)',
        strokeColor         : 'rgba(60,141,188,0.8)',
        pointColor          : '#3b8bba',
        pointStrokeColor    : 'rgba(60,141,188,1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data                : <?php echo $return; ?>
      }
    ]
  }
  barChartData.datasets[1].fillColor   = '#00a65a'
  barChartData.datasets[1].strokeColor = '#00a65a'
  barChartData.datasets[1].pointColor  = '#00a65a'
  var barChartOptions                  = {
    scaleBeginAtZero        : true,
    scaleShowGridLines      : true,
    scaleGridLineColor      : 'rgba(0,0,0,.05)',
    scaleGridLineWidth      : 1,
    scaleShowHorizontalLines: true,
    scaleShowVerticalLines  : true,
    barShowStroke           : true,
    barStrokeWidth          : 2,
    barValueSpacing         : 5,
    barDatasetSpacing       : 1,
    responsive              : true,
    maintainAspectRatio     : true
  }

  barChartOptions.datasetFill = false
  var myChart = barChart.Bar(barChartData, barChartOptions)
  document.getElementById('legend').innerHTML = myChart.generateLegend();
});

$(function(){
  $('#select_year').change(function(){
    window.location.href = 'home.php?year='+$(this).val();
  });
});
</script>

</body>
</html>