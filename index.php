<?php

$searchTitle = "";
$total = 2000;
$rangeMin = 0;
$rangeMax = $total;
$rangeVal = "0-0";
$rangeValArray = array();
$pos = 0;

$sql = "SELECT * FROM `2016`";

//Search for the title
if(!empty($_GET['searchTitle'])){
  $searchTitle = preg_replace("#[^a-z0-9 ]#i", "", $_GET['searchTitle']);

  $sql .= " WHERE artist LIKE '%{$searchTitle}%' OR title LIKE '%{$searchTitle}%'";
}

//Search for the position
if(!empty($_GET['position']) && $_GET['position'] != 0){
  $pos = preg_replace("#[^0-9]#i", "", $_GET['position']);

  $sql .= "WHERE position='$pos'";
}

//Search with a range
if(!empty($_GET['range'])){
  $rangeVal = preg_replace("#[^0-9\-]#i", "", $_GET['range']);

  $rangeValArray = explode("-", $rangeVal);

  if(!empty($rangeValArray[0])){
    $rangeMin = $rangeValArray[0];
  }
  if(!empty($rangeValArray[1])){
    $rangeMax = $rangeValArray[1];
  }

  if($_GET['position'] == 0){
    if(!empty($_GET['searchTitle'])){
      $sql .= " AND ";
    }else{
      $sql .= " WHERE ";
    }
    $sql .= " position BETWEEN ".($rangeMin)." AND ".($rangeMax);
  }
}

$sql .= " ORDER BY top2000id DESC";

//die($sql);

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "top2000";
$db_conx = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}/*  else {
	echo "Successful database connection, happy coding!!!";
}*/
$data = array();
$query = mysqli_query($db_conx, $sql);
$len  = mysqli_num_rows($query);
//$rangeMax = $len;
//$total = $len;
if($len > 0){
  $ind=0;
  while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $data[$ind]["position"] = $row["position"];
    $data[$ind]["title"] = $row["title"];
    $data[$ind]["artist"] = $row["artist"];
    $data[$ind]["year"] = $row["year"];
    $data[$ind]["playtime"] = $row["playtime"];
    $ind++;
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Top 2000</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />

    <style>
      body{
        margin-top: 40px;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-3" style="position: fixed; float: left;">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <div class="form-group">
              <label for="searchTitle">Search Title / Artist</label>
              <input type="text" class="form-control" id="searchTitle" name="searchTitle" placeholder="Search Title / Artist" value="<?php echo $searchTitle; ?>" />
            </div>

            <div class="form-group">
              <label for="position">Position</label>
              <input type="number" class="form-control" id="position" name="position" value="<?php echo $pos; ?>" />
            </div>

            <div class="form-group">
              <label for="slider">Range (min - max): </label>
              <input type="text" id="range-selected" readonly name="range" style="border:0; color:#f6931f; font-weight:bold;">
              <div class="slider" id="slider"></div>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
            <button type="button" onclick="resetForm();" class="btn btn-danger">Reset</button>
          </form>
        </div>

        <div class="col-xs-9" style="float: right;">
          <table class="table table-striped table-bordered">
            <thead>
              <th>Position</th>
              <th>Title</th>
              <th>Artist</th>
              <th>Year</th>
              <th>Played at</th>
            </thead>
            <?php
            foreach($data AS $item) {
            ?>
            <tr>
              <td><?php echo $item["position"]; ?></td>
              <td><?php echo $item["title"]; ?></td>
              <td><?php echo $item["artist"]; ?></td>
              <td><?php echo $item["year"]; ?></td>
              <td><?php echo $item["playtime"]; ?></td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <div style="clear: both;"></div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script>
	  function resetForm(){
		window.location.replace("<?php echo $_SERVER["PHP_SELF"]; ?>");
	  }
      $(function(){
        $(".slider").slider({
          max: <?php echo $total;?>,
          range: true,
          values: [<?php echo $rangeMin; ?>, <?php echo $rangeMax; ?>],
          slide: function(event, ui) {
            $("#range-selected").val(ui.values[ 0 ] + "-" + ui.values[ 1 ]);
          }
        });
        $("#range-selected").val(
          $(".slider").slider("values", 0) +"-" + $(".slider").slider("values", 1)
        );
      });
    </script>
  </body>
</html>
