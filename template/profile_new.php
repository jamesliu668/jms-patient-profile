<?php
    $action = "new-save";
    $fieldJson = "{\"fields\": [";
    foreach ($result as $field) {
        $fieldJson .= "{\"slug\": \"".$field["slug"]."\", \"name\":\"".$field["name"]."\", \"type\":".$field["type"].", \"description\":\"".$field["description"]."\"},";
    }
    $fieldJson = substr($fieldJson, 0, -1);
    $fieldJson .= "]}";
?>
<div class="wrap">
<h1>
<?php
    echo __('Create New Profile','jms-patient-profile');
?>
</h1>

<form method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'new_profile' ); ?>
    <input type="hidden" name="fields" value="<?php echo urlencode(stripslashes($fieldJson)); ?>"/>
    
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="profile_title"><?php echo __('Title','jms-patient-profile'); ?></label></th>
            <td>
                <input name="profile_title" type="text" id="profile_title" value="" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="userid"><?php echo __('Choose User','jms-patient-profile'); ?></label></th>
            <td>
                <select name="userid" id="userid">
            
            <?php
                // Array of WP_User objects.
                foreach ( $blogusers as $user ) {
                    echo '<option value="'.$user->ID.'">'.$user->user_login.'-'.$user->user_lastname.$user->user_firstname.'</option>';
                }
            ?>
                </select>
            </td>
        </tr>
        
        <?php
            foreach ($result as $field) {
                switch($field["type"]) {
                    case 1:
                    default:
                        ?>
        <tr>
            <th scope="row"><label for="<?php echo $field["slug"]?>"><?php echo stripslashes($field["name"]); ?></label></th>
            <td>
                <input name="<?php echo $field["slug"]?>" type="text" id="<?php echo $field["slug"]?>" value="" class="regular-text">
                <p class="description" id="tagline-description"><?php echo stripslashes($field["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 2:
                        ?>
        <tr>
            <th scope="row"><label for="<?php echo $field["slug"]?>"><?php echo stripslashes($field["name"]); ?></label></th>
            <td>
                <input onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="<?php echo $field["slug"]?>" type="text" id="<?php echo $field["slug"]?>" value="" class="regular-text">
                <p class="description" id="tagline-description"><?php echo stripslashes($field["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 3:
                    case 4:
                        ?>
        <!-- At current stage, we just text instead of file -->
        <tr>
            <th scope="row"><label for="<?php echo $field["slug"]?>"><?php echo stripslashes($field["name"]); ?></label></th>
            <td>
                <input name="<?php echo $field["slug"]?>" type="text" id="<?php echo $field["slug"]?>" class="regular-text">
                <p class="description" id="tagline-description"><?php echo stripslashes($field["description"]);?></p>
            </td>
        </tr>
                        <?php
                        break;
                    case 5:
                    ?>
        <tr>
            <th scope="row"><label for="<?php echo $field["slug"]?>"><?php echo stripslashes($field["name"]);?></label></th>
            <td>
                <textarea name="<?php echo $field["slug"];?>" id="<?php echo $field["slug"];?>" rows="5" cols="53"></textarea>
                <p class="description" id="tagline-description"><?php echo stripslashes($field["description"]);?></p></td>
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