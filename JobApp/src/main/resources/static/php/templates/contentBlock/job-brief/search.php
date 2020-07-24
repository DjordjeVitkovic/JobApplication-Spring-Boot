<div class="form-group">
    <div class="p-form-cg">
        <?php foreach($data['serviceData']['search']['results'] as $job){ ?>
            <div class="radio">
                <label>
                    <input type="radio" value="<?php echo $job['jobNumber'] ?>" name="__jobNumber">
                    <span class="p-check-icon"><span class="p-check-block"></span></span>
                    <span class="p-label"><?php echo $job['jobNumber'] ?></span>
                </label>
            </div>
        <?php } ?>
    </div>
</div>