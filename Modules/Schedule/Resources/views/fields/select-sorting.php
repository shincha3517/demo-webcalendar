<div class="form-group">
    <label for="<?php echo $settingName; ?>">Sorting options</label>
    <select class="form-control" name="<?php echo $settingName; ?>" id="<?php echo $settingName; ?>">
        <option value="1" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 1 ? 'selected' : '' ?>>Subject (default)</option>
        <option value="2" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 2 ? 'selected' : '' ?>>Number of relief</option>
        <option value="3" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 3 ? 'selected' : '' ?>>Fewer lessons</option>
    </select>
</div>
