<?php 

 $newId = MenuLinks::getNewId();
?>



<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption"><i class="fa fa-plus"></i>Создать пункт меню</div>
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

                    echo CHtml::beginForm('','post', array('id' => 'menu-form', 'class' => 'form-horizontal'));
                ?>
                <div class="form-body">
                <?php

                echo CHtml::errorSummary($model);
                ?>

                <div class="row">
                    <div class="col-md-5 col-lg-7">
                        <div class="form-group">
                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'id'); ?> </label>
                            <div class="col-md-9 ">
                                <?php  echo CHtml::activeTextField($model,'id', array('value' => $newId,'class' => 'form-control','readonly' => true)) . '<br>'; ?>
                            </div>

                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'title'); ?> </label>
                            <div class="col-md-9 ">
                                <?php  echo CHtml::activeTextField($model,'title', array('class' => 'form-control')) . '<br>'; ?>
                            </div>

                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'url'); ?> </label>
                            <div class="col-md-9">
                                <?php  echo CHtml::activeTextField($model,'url', array('class' => 'form-control')) . '<br>'; ?>
                            </div>

                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'controller'); ?> </label>
                            <div class="col-md-9">
                                <?php  echo CHtml::activeTextField($model,'controller', array('class' => 'form-control')) . '<br>'; ?> 
                            </div>


                            <label class="control-label col-md-3"><?php echo CHtml::activeLabel($model, 'parent_id'); ?></label>
                            <div class="col-md-9">
                                <?php
                                    echo CHtml::activeDropDownList($model, 'parent_id', $sections, array('encode'=>false,'class'=>'form-control', $model->title =>array('selected' => 'selected'))) . '<br>';;
                                ?>
                            </div>

                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'icon'); ?> </label>
                            <div class="col-md-9">
                                    <?php   echo  CHtml::activeTextField($model,'icon', array('class' => 'form-control')) . '<br>'; ?>
                            </div>

                            <!-- Порядок нельзя указывать при создании -->
                            <!--<label class="control-label col-md-3"> <?php # echo CHtml::activeLabel($model, 'order'); ?> </label>
                            <div class="col-md-9">
                                <?php #   echo CHtml::activeTextField($model,'order', array('class'=>'form-control touchspin_retries')) . '<br>'; ?>
                            </div>-->

                            <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'is_visible'); ?> </label>
                            <div class="col-md-9">
                                <div class="make-switch" data-on="success" data-off="danger">
                                    <?php  echo CHtml::activeCheckBox($model, 'is_visible', array('type' => 'checkbox', 'class'=>'toggle')); ?>
                                </div>
                            </div>
                            

                        </div>
                    </div>
                </div>

                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-offset-3 col-md-9">
                                <?php echo CHtml::submitButton('Обновить', array('class' => 'btn blue', 'value' => 'сохранить')); ?>
                                <?php echo CHtml::button('Отменить', array('class' => 'btn default','onclick' => 'js:document.location.href="/menueditor/menulinks/'.'"')); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </div>

                <!-- Подпункты (только для главных пунктов) -->
                <?php if($subsections) { ?>
                  <h3 class="form-section">Подпункты</h3>
                    <div class="row">
                        <div class="col-md-5 col-lg-7">
                            <ol>
                                <?php 
                                foreach ($subsections as $subsection) 
                                { 
                                     echo "<li>";
                                     echo CHtml::link($subsection['title'], array(MenuLinks::model()->getModelName() . '/update/id/' . $subsection['id']));
                                     echo "</li>";
                                 
                                 } ?>
                            </ol>
                        </div>
                    </div>
                <?php } ?>

                

        
                </div>
                <?php echo CHtml::endForm();?>
            </div>
        </div>
    </div>
