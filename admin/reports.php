<?php 
if (!function_exists('base_url')) {
    function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>MIndcheck/patient_assets/vendor/bootstrap/../css/highcharts.css" rel="stylesheet">
    <title>patient</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    <?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    //import database
    include("../connection.php");

    $sqlmain= "SELECT rep.*, me.*, dept.description as deptDescription, sem.description as semesterDescription FROM `reportform` rep INNER JOIN patient me ON me.pid = rep.patientid INNER JOIN department dept ON dept.id = me.paddress INNER JOIN semester sem ON sem.id = me.ptel;";

    $reportList = $database->query($sqlmain);
    
    ?>
    <div class="container">
        <?php include("admin_header.php"); ?>
        <div class="dash-body">

            <div id="socialmedia" style="margin-bottom: 1em;" class="chart-display"></div>
            <div id="semester" style="margin-bottom: 1em;" class="chart-display"></div>
            <div id="department" style="margin-bottom: 1em;" class="chart-display"></div>
            <div id="casetype" style="margin-bottom: 1em;" class="chart-display"></div>
            <div id="patienttype" style="margin-bottom: 1em;" class="chart-display"></div>

            <table id="reports" class="table table-striped display" style="width:100%">
                <thead>
                    <tr>
                        <th>SAP ID</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Patient Email</th>
                        <th>Patient Type</th>
                        <th>Case Type</th>
                        <th>Social Media </th>
                        <th>Category</th>
                        <th>Effects</th>
                        <th>Comments</th>
                        <th>Patient Email</th>
                        <th>Submit Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if($reportList->num_rows>0){
                            for ( $x=0; $x<$reportList->num_rows;$x++)
                            {
                                $row=$reportList->fetch_assoc();
                    ?>
                                <tr>
                                    <td><?php echo $row["pnic"]; ?></td>
                                    <td><?php echo $row["deptDescription"]; ?></td>
                                    <td><?php echo $row["semesterDescription"]; ?></td>
                                    <td><?php echo $row["pemail"]; ?></td>
                                    <td><?php echo $row["patienttype"]; ?></td>
                                    <td><?php echo $row["casetype"]; ?></td>
                                    <td><?php echo $row["socialmedia"]; ?></td>
                                    <td><?php echo ($row["discriminatoryharrasment"] == null ? "" : $row["discriminatoryharrasment"]) .", ". ($row["sexualharrasement"] == null ? "" : $row["sexualharrasement"]) .", ". ($row["cyberbullying"] == null ? "" : $row["cyberbullying"]) .", ". ($row["relationshipbreakdown"] == null ? "" : $row["relationshipbreakdown"]); ?></td>
                                    <td><?php echo ($row["panicattacks"] == null ? "" : $row["panicattacks"]) .", ". ($row["anxiety"] == null ? "" : $row["anxiety"]) .", ". ($row["depression"] == null ? "" : $row["depression"]); ?></td>
                                    <td><?php echo $row["comments"]; ?></td>
                                    <td><?php echo $row["doctorid"]; ?></td>
                                    <td><?php echo $row["submissiondate"]; ?></td>
                                </tr>
                    <?php 
                            }
                        }
                        else{
                        ?>
                            <tr>
                                <td colspan="12"> No record found ... </td>
                            </tr>
                        <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>

</body>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/script/jquery.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/jszip.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/pdfmake.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/vfs_fonts.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/highcharts.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    var table = $('#reports').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
            ]
        } );

    // Create chart
    const socialchart = Highcharts.chart('socialmedia', {
        chart: {
            type: 'pie',
            styledMode: true
        },
        title: {
            text: 'Social Media Count'
        },
        series: [
            {
                data: chartData(table)
            }
        ]
    });

    const deptchart = Highcharts.chart('department', {
        chart: {
            type: 'pie',
            styledMode: true
        },
        title: {
            text: 'Department Wise Info'
        },
        series: [
            {
                data: chartData(table)
            }
        ]
    });

    const semesterchart = Highcharts.chart('semester', {
        chart: {
            type: 'column',
            styledMode: true
        },
        title: {
            text: 'Semester Wise Breakup'
        },
        series: [
            {
                data: chartData(table)
            }
        ]
    });
    const patienttypechart = Highcharts.chart('patienttype', {
        chart: {
            type: 'line',
            styledMode: true
        },
        title: {
            text: 'Patient Type Analysis'
        },
        series: [
            {
                data: chartData(table)
            }
        ]
    });

    const casetypechart = Highcharts.chart('casetype', {
        chart: {
            type: 'scatter',
            styledMode: true
        },
        title: {
            text: 'Case Type View'
        },
        series: [
            {
                data: chartData(table)
            }
        ]
    });
     
    // On each draw, update the data in the chart
    table.on('draw', function () {
        deptchart.series[0].setData(chartData(table, 1));
        semesterchart.series[0].setData(chartData(table, 2));
        patienttypechart.series[0].setData(chartData(table, 4));
        casetypechart.series[0].setData(chartData(table, 5));
        socialchart.series[4].setData(chartData(table, 6));
    });
    
    } );
    function chartData(table, columnIndex) {
        var counts = {};
     
        // Count the number of entries for each position
        table
            .column(columnIndex, { search: 'applied' })
            .data()
            .each(function (val) {
                if (counts[val]) {
                    counts[val] += 1;
                }
                else {
                    counts[val] = 1;
                }
            });
     
        return Object.entries(counts).map((e) => ({
            name: e[0],
            y: e[1]
        }));
    }
</script>
</html>