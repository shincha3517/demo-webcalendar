<div class="form-group">
    <div class="checkbox">
        <label for="">
            <div class="icheckbox_flat-blue">
                <input type="checkbox" name="sorting_condition_1" value="" class="flat-blue" />
            </div>
            Prioritise those within the same subject
        </label>
    </div>


    <label for="<?php echo $settingName; ?>">Sorting options</label>

    <br/>
    <input type="checkbox" name="sorting_condition_1" value="" /> Prioritise those within the same subject
    <br/>
    <input type="checkbox" name="sorting_condition_2" value="" /> Prioritise User with fewer lessons before and after the assigned relief lesson
    <br/>
    <input type="checkbox" name="sorting_condition_2" value="" /> Prioritise User with number relief made by week, term, year



    <select class="form-control" name="<?php echo $settingName; ?>" id="<?php echo $settingName; ?>">
        <option value="1" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 1 ? 'selected' : '' ?>>Subject (default)</option>
        <option value="2" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 2 ? 'selected' : '' ?>>Number of relief</option>
        <option value="3" <?php echo isset($dbSettings[$settingName]) && $dbSettings[$settingName]->plainValue == 3 ? 'selected' : '' ?>>Fewer lessons</option>
    </select>
</div>
