<?php echo form_open("", array("id" => "environmental_records-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
        <label for="date_filed" class="col-md-3"><?php echo lang('date_filed'); ?></label>
        <div class="col-md-9">
            <?php
                echo get_date_format($model_info->date, $model_info->id_project);
            ?>
        </div>
    </div>

    <!-- DATOS SINOPTICOS 24 HRS -->
    <div class="form-group">
        <label for="pmca_24_hrs_t1" class=" col-md-3"><?php echo lang('pmca_24_hrs_t1'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_24_hrs_t1; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_24_hrs_t1; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_24_hrs_t1; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_24_hrs_t2" class=" col-md-3"><?php echo lang('pmca_24_hrs_t2'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_24_hrs_t2; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_24_hrs_t2; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_24_hrs_t2; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_24_hrs_t3" class=" col-md-3"><?php echo lang('pmca_24_hrs_t3'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_24_hrs_t3; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_24_hrs_t3; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_24_hrs_t3; 
            ?>
        </div>
    </div>

    <!-- DATOS SINOPTICOS 48 HRS -->
    <div class="form-group">
        <label for="pmca_48_hrs_t1" class=" col-md-3"><?php echo lang('pmca_48_hrs_t1'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_48_hrs_t1; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_48_hrs_t1; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_48_hrs_t1; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_48_hrs_t2" class=" col-md-3"><?php echo lang('pmca_48_hrs_t2'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_48_hrs_t2; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_48_hrs_t2; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_48_hrs_t2; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_48_hrs_t3" class=" col-md-3"><?php echo lang('pmca_48_hrs_t3'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_48_hrs_t3; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_48_hrs_t3; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_48_hrs_t3; 
            ?>
        </div>
    </div>







    <div class="form-group">
        <label for="pmca_72_hrs_t1" class=" col-md-3"><?php echo lang('pmca_72_hrs_t1'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_72_hrs_t1; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_72_hrs_t1; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_72_hrs_t1; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_72_hrs_t2" class=" col-md-3"><?php echo lang('pmca_72_hrs_t2'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_72_hrs_t2; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_72_hrs_t2; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_72_hrs_t2; 
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="pmca_72_hrs_t3" class=" col-md-3"><?php echo lang('pmca_72_hrs_t3'); ?></label>
        <div class=" col-md-9">
            <?php 
                echo "<strong>PMCA:</strong> ".$pmca_72_hrs_t3; 
                echo "<br>";
                echo "<strong>ws_margarita_str:</strong> ".$ws_margarita_str_pmca_72_hrs_t3; 
                echo "<br>";
                echo "<strong>hora_ws_min:</strong> ".$hora_ws_min_pmca_72_hrs_t3; 
            ?>
        </div>
    </div>

    <!--
    <div class="form-group">
        <label for="backup_document" class=" col-md-3"><?php echo lang('backup_document'); ?></label>
        <div class=" col-md-9">
            <?php echo "<table><tr><td class='option' style='padding: 0px !important;'>".$html_evidence_file."</td></tr></table>"; ?>
        </div>
    </div>
    -->

    <!--
    <div class="form-group">
        <label for="observations" class=" col-md-3"><?php echo lang('observations'); ?></label>
        <div class=" col-md-9">
            <?php echo $model_info->observations ? $model_info->observations : "-"; ?>
        </div>
    </div>
    -->

	<div class="form-group">
        <label for="created_by" class="col-md-3"><?php echo lang('created_by'); ?></label>
        <div class="col-md-9">
            <?php
			echo $created_by;
            ?>
        </div>
    </div>
    
    <!--
    <div class="form-group">
        <label for="modified_by" class="col-md-3"><?php echo lang('modified_by'); ?></label>
        <div class="col-md-9">
            <?php
            echo $modified_by;
            ?>
        </div>
    </div>
    -->

	<div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
			echo time_date_zone_format($model_info->created, $model_info->id_project);
            ?>
        </div>
    </div>
    
    <!--
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model_info->modified)?time_date_zone_format($model_info->modified, $model_info->id_project):'-';
            ?>
        </div>
    </div>
    -->

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
	$(document).ready(function(){
		
		//$('[data-toggle="tooltip"]').tooltip();
		
	});
</script>    