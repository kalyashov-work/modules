<?php 

 
?>

<div class="portlet box green">
     <div class="portlet-title">
         <div class="caption"><i class="fa fa-pencil"></i>Редактирование пункта</div>
            <div class="tools">
                <a href="javascript:;" class="collapse"></a>
                <a href="#portlet-config" data-toggle="modal" class="config"></a>
                <a href="javascript:;" class="reload"></a>
                <a href="javascript:;" class="remove"></a>
            </div>
    </div>   
</div>
<div class="span10">
    <div class="portlet-body form">
        <?php
            echo CHtml::beginForm('','post', array('id' => 'devices-form', 'class' => 'form-horizontal'));
        ?>
        <div class="form-body">
            <?php
                echo CHtml::errorSummary($model);
            ?>

            <h3 class="form-section">Настройки главного пункта</h3>
            <h3 class="form-section">Настройки подпункта</h3>
  
            <div class="row">
                <div class="col-md-5 col-lg-7">
                    <div class="form-group">
                        <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'title'); ?> </label>
                        <div class="col-md-9 ">
                            <?php  echo CHtml::activeTextField($model,'title', array('class' => 'form-control')) . '<br>'; ?>
                        </div>

                       
                        <label class="control-label col-md-3"><?php echo CHtml::activeLabel($model, 'parent_id'); ?></label>
                        <div class="col-md-9">
                            <?php
                                echo CHtml::activeDropDownList($model, 'parent_id', $nodes, array('encode'=>false,'class'=>'form-control', $model->title =>array('selected' => 'selected'))) . '<br>';;
                            ?>
                        </div>

                        <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'order'); ?> </label>
                        <div class="col-md-9">
                            <?php   echo CHtml::activeTextField($model,'order', array('class'=>'form-control touchspin_retries')) . '<br>'; ?>
                        </div>

                        
                               
                    </div>
                </div>
            </div>

            <div class="form-actions fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-offset-3 col-md-9">
                            <?php echo CHtml::submitButton('Обновить', array('class' => 'btn blue', 'value' => 'сохранить')); ?>
                            <?php echo CHtml::button('Отменить', array('class' => 'btn default','onclick' => 'js:document.location.href="/configuration/menulinks' . '"')); ?>
                            <?php echo CHtml::button('Удалить', array('class' => 'btn red','onclick' => 'js:document.location.href="/configuration/menulinks/delete/id/' . $model['id'] . '"')); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>

          
            <?php echo CHtml::endForm();?>
    </div>
</div>
