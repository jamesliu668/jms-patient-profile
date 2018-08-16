<?php
    $action = "update-save";

    $fieldsObj = json_decode($result[0]["content"], true)["fields"];

    $fieldJson = "{\"fields\": [";
    $fieldLength = count($fieldsObj);

    for($i = 0; $i < $fieldLength; $i++) {
        $field = &$fieldsObj[$i];
        foreach ($fieldsInDB as $k=>$fieldInDB) {
            if($field["slug"] == $fieldInDB["slug"]) {
                //$field["slug"] = $fieldInDB["slug"];
                $field["name"] = $fieldInDB["name"];
                $field["type"] = $fieldInDB["type"];
                $field["description"] = $fieldInDB["description"];

                unset($fieldsInDB[$k]);
            }
        }

        $fieldJson .= "{\"slug\": \"".$field["slug"]."\", \"name\":\"".$field["name"]."\", \"type\":".$field["type"].", \"description\":\"".$field["description"]."\"},";
    }

    foreach ($fieldsInDB as $newField) {
        array_push($fieldsObj, $newField);
        $fieldJson .= "{\"slug\": \"".$newField["slug"]."\", \"name\":\"".$newField["name"]."\", \"type\":".$newField["type"].", \"description\":\"".$newField["description"]."\"},";
    }

    $fieldJson = substr($fieldJson, 0, -1);
    $fieldJson .= "]}";
?>
<div class="wrap">
<h1>
<?php
    echo __('Edit Profile','jms-patient-profile');
?>
</h1>

<form method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'update_profile' ); ?>
    <input type="hidden" name="fields" value="<?php echo urlencode(stripslashes($fieldJson)); ?>"/>
    <input type="hidden" name="id" value="<?php echo $result[0]["id"]; ?>"/>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="profile_title"><?php echo __('Title','jms-patient-profile'); ?></label></th>
            <td>
                <input name="profile_title" type="text" id="profile_title" value="<?php echo stripslashes($result[0]["title"]); ?>" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="userid"><?php echo __('Choose User','jms-patient-profile'); ?></label></th>
            <td>
                <select name="userid" id="userid">
            
            <?php
                // Array of WP_User objects.
                foreach ( $blogusers as $user ) {
                    if($user->ID == $result[0]["userid"]) {
                        echo '<option selected value="'.$user->ID.'">'.$user->user_login.'-'.$user->user_lastname.$user->user_firstname.'</option>';
                    } else {
                        echo '<option value="'.$user->ID.'">'.$user->user_login.'-'.$user->user_lastname.$user->user_firstname.'</option>';
                    }
                }
            ?>
                </select>
            </td>
        </tr>
        
        <?php
            foreach ($fieldsObj as $fieldObj) {
                switch($fieldObj["type"]) {
                    case 1:
                    default:
                        ?>
        <tr>
            <th scope="row"><label for="<?php echo $fieldObj["slug"]?>"><?php echo stripslashes($fieldObj["name"]); ?></label></th>
            <td>
                <input name="<?php echo $fieldObj["slug"]?>" type="text" id="<?php echo $fieldObj["slug"]?>" value="<?php echo stripslashes($fieldObj["data"]); ?>" class="regular-text">
                <p class="description" id="tagline-description"><?php echo stripslashes($fieldObj["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 2:
                        ?>
        <tr>
            <th scope="row"><label for="<?php echo $fieldObj["slug"]?>"><?php echo stripslashes($fieldObj["name"]); ?></label></th>
            <td>
                <input onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="<?php echo $fieldObj["slug"]?>" type="text" id="<?php echo $fieldObj["slug"]?>" value="<?php echo stripslashes($fieldObj["data"]); ?>" class="regular-text">
                <p class="description" id="tagline-description"><?php echo stripslashes($fieldObj["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 3:
                    case 4:
                        ?>
        <!-- At current stage, we just text instead of file -->
        <tr>
            <th scope="row"><label for="<?php echo $fieldObj["slug"]?>"><?php echo stripslashes($fieldObj["name"]); ?></label></th>
            <td>
                <input name="<?php echo $fieldObj["slug"]?>" type="text" id="<?php echo $fieldObj["slug"]?>" class="regular-text" value="<?php echo stripslashes($fieldObj["data"]); ?>">
                <p class="description" id="tagline-description"><?php echo stripslashes($fieldObj["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 5:
                    ?>
        <tr>
            <th scope="row"><label for="<?php echo $fieldObj["slug"]?>"><?php echo stripslashes($fieldObj["name"]);?></label></th>
            <td>
                <textarea name="<?php echo $fieldObj["slug"];?>" id="<?php echo $fieldObj["slug"];?>" rows="5" cols="53"><?php echo stripslashes($fieldObj["data"]); ?></textarea>
                <p class="description" id="tagline-description"><?php echo stripslashes($fieldObj["description"]);?></p></td>
            </td>
        </tr>
                    <?php
                        break;
                }
        ?>

        <?php
            }
        ?>

	</tbody>
</table>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save','jms-patient-profile');?>">
<a class="button" style="margin-left: 10px;" onclick="window.history.back();"><?php echo __('Cancel','jms-patient-profile');?></a>
</p>

</form>

</div>