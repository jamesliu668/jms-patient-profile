<?php
    $profileListURL = site_url("/jms-profile/")."?jms_patient_profile_id=".$current_profile_id;
    $listMsg = sprintf(__("<a href=\"%s\">返回</a>", "jms-patient-profile"), $profileListURL);

    echo "<div class='jms-profile-nav'>".$listMsg."</div>";
?>

<div class="chart">

<canvas id="myChart" width="900" height="450"></canvas>

</div>

<script>

var mydata = [<?php
                $dataListStr = "";
                foreach($history as $item) {
                    $dataListStr .= $item["value"].",";
                }
                $dataListStr = substr($dataListStr, 0, -1);
                echo $dataListStr;
            ?> ];

var maxValue = Math.max(<?php echo $dataListStr; ?>);
var minValue = Math.min(<?php echo $dataListStr; ?>);
maxValue = Math.ceil(maxValue + maxValue * 0.1);
minValue = Math.floor(minValue - minValue * 0.1);


    jQuery( document ).ready(function() {


var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php
            $labelListStr = "";
            foreach($history as $item) {
                $labelListStr .= "\"".$item["title"]."\",";
            }
            echo substr($labelListStr, 0, -1);
        ?>],
        datasets: [{
            label: '<?php echo $history[0]["name"];?>',
            data: [<?php
                $dataListStr = "";
                foreach($history as $item) {
                    $dataListStr .= $item["value"].",";
                }
                echo substr($dataListStr, 0, -1);
            ?>],
            backgroundColor: [
                <?php
                    $colorStr = "";
                    foreach($history as $item) {
                        $colorStr .= "'rgba(54, 162, 235, 0.9)',";
                    }
                    echo substr($colorStr, 0, -1);
                ?>
            ], //must be an array, check following function generateLabels();
            borderColor: [
                <?php
                    $colorStr = "";
                    foreach($history as $item) {
                        $colorStr .= "'rgba(54, 162, 235, 0.9)',";
                    }
                    echo substr($colorStr, 0, -1);
                ?>
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        //maintainAspectRatio: true,
        legend: {
            onClick: function(event, legendItem) {
                //do nothing, which will override the default behaviors
                //console.log(event);
                console.log(legendItem);
            },
            labels: {
                generateLabels: function(chart) {
                    var data = chart.data;
                    return data.datasets.map(function(dataset, i) {
                        return {
                            text: dataset.label + " <?php echo $history[0]["description"]; ?>",
                            fillStyle: dataset.backgroundColor[0],
                            hidden: !chart.isDatasetVisible(i),
                            lineCap: dataset.borderCapStyle,
                            lineDash: dataset.borderDash,
                            lineDashOffset: dataset.borderDashOffset,
                            lineJoin: dataset.borderJoinStyle,
                            lineWidth: dataset.borderWidth,
                            strokeStyle: dataset.borderColor,
                            pointStyle: dataset.pointStyle,

                            // Below is extra data used for toggling the datasets
                            datasetIndex: i
                        };
                    }, this);
                }
            }
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    //copy from chart.js source code:
                    //https://github.com/chartjs/Chart.js/blob/master/src/core/core.tooltip.js
                    var label = data.datasets[tooltipItem.datasetIndex].label || '';

                    if (label) {
                        label += ': ';
                    }
                    label += tooltipItem.yLabel;
                    label += " <?php echo $history[0]["description"]; ?>";
                    return label;
			    },
            },
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true,
                    min: minValue,
                    max: maxValue,
                    stepSize: (maxValue - minValue) / 3
                }
            }]
        },

    }
});
    });

</script>

<?php
    echo "<div class='jms-profile-nav'>".$listMsg."</div>";
?>