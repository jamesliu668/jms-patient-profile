<?php

    $fieldID = "";
    $fieldName = "";
    $fieldType = 0;
    $fieldWeight = 0;
    $fieldDesc = "";
    $action = "new-save";
    if(isset($result)) {
        $fieldID = $result[0]["id"];
        $fieldName = stripslashes($result[0]["name"]);
        $fieldType = $result[0]["type"];
        $fieldWeight = $result[0]["weight"];
        $fieldDesc = stripslashes($result[0]["description"]);
        $action = "update-save";
    }
?>
<div class="wrap">
<h1>
<?php
    echo __('Create New Profile Item','jms-patient-profile');
?>
</h1>

<form method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'new_profile_field' ); ?>
    <input type="hidden" name="id" value="<?php echo $fieldID; ?>">
    
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="itemname"><?php echo __('Field Name','jms-patient-profile'); ?></label></th>
            <td><input name="itemname" type="text" id="itemname" value="<?php echo $fieldName; ?>" class="regular-text"></td>
        </tr>
        
        <tr>
            <th scope="row"><label for="itemtype"><?php echo __('Field Type','jms-patient-profile');?></label></th>
            <td>
                <select name="itemtype" id="itemtype">
                    <option <?php if($fieldType == 1) echo "selected=\"selected\""; ?> value="1"><?php echo __('Text','jms-patient-profile');?></option>
                    <option <?php if($fieldType == 2) echo "selected=\"selected\""; ?> value="2"><?php echo __('Number','jms-patient-profile');?></option>
                    <option <?php if($fieldType == 3) echo "selected=\"selected\""; ?> value="3"><?php echo __('Image','jms-patient-profile');?></option>
                    <option <?php if($fieldType == 4) echo "selected=\"selected\""; ?> value="4"><?php echo __('File','jms-patient-profile');?></option>
                    <option <?php if($fieldType == 5) echo "selected=\"selected\""; ?> value="5"><?php echo __('Paragraph','jms-patient-profile');?></option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="itemorder"><?php echo __('Field Order','jms-patient-profile');?></label></th>
            <td><input name="itemorder" type="text" id="itemorder" aria-describedby="tagline-description" value="<?php echo $fieldWeight; ?>" class="regular-text" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            <p class="description" id="tagline-description"><?php echo __('Default value is "0", you can set this value as 0-99 to change this field order in profile','jms-patient-profile');?></p></td>
        </tr>
        
        <tr>
            <th scope="row"><label for="itemdesc"><?php echo __('Field Description','jms-patient-profile');?></label></th>
            <td><input name="itemdesc" type="text" id="itemdesc" aria-describedby="tagline-description" value="<?php echo $fieldDesc; ?>" class="regular-text">
            <p class="description" id="tagline-description"><?php echo __('(Optional) A short description','jms-patient-profile');?></p></td>
        </tr>
	</tbody>
</table>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save','jms-patient-profile');?>">
<a class="button" style="margin-left: 10px;" onclick="window.history.back();"><?php echo __('Cancel','jms-patient-profile');?></a>
</p>

</form>

</div>