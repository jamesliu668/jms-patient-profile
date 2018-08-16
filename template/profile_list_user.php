<?php
	echo "<div class='jms-profile-listu'>";
    foreach($result as $data) {
        echo "<div><a href=\"./?jms_patient_profile_id=".$data["id"]."\">".stripslashes($data["title"])."</a></div>";
    }
	echo "</div>";
?>