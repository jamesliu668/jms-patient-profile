<?php
    $profileListURL = site_url("/jms-profile/");
    $listMsg = sprintf(__("<a href=\"%s\">Back to List</a>", "jms-patient-profile"), $profileListURL);

    echo "<div class='jms-profile-nav'>".$listMsg."</div>";
?>

<div class="jms-profile-listd">
<table cellspacing="0" cellpadding="0" class="jms-patient-profile">
<tbody>

<?php
    $fieldsObj = json_decode($result[0]["content"], true)["fields"];
    $rowIndex = 0;
    foreach ($fieldsObj as $field) {
        if($rowIndex % 2 == 0) {
            echo "<tr class=\"even\">";
        } else {
            echo "<tr>";
        }
        
        if($field["type"] == 2) {
            echo "<th><a class=\"trackable-field\" href=\"./?current_profile_id=".$profileID."&track_slug=".$field["slug"]."\">".stripslashes($field["name"])."</a></th><td>".nl2br(stripslashes($field["data"]))." ".nl2br(stripslashes(showUnit($field["slug"])))."</td></tr>";
        } else {
            echo "<th>".stripslashes($field["name"])."</th><td>".nl2br(stripslashes($field["data"]))."</td></tr>";
        }

    }
?>

</tbody></table></div>


<?php
    echo "<div class='jms-profile-nav'>".$listMsg."</div>";
?>