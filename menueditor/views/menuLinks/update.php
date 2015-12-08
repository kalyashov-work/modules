<?php 

 
?>

<div class="portlet box green">
     <div class="portlet-title">
         <div class="caption"><i class="fa fa-pencil"></i>Редактирование пункта меню</div>
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

            <?php if($subsections) { ?>
                <h3 class="form-section">Настройки главного пункта</h3>
            <?php } else { ?>
                <h3 class="form-section">Настройки подпункта</h3>
            <?php } ?>

            <div class="row">
                <div class="col-md-5 col-lg-7">
                    <div class="form-group">
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


                        <label class="control-label col-md-3"><?php  echo CHtml::activeLabel($model, 'parent_id'); ?></label>
                        <div class="col-md-9">
                            <?php
                                echo CHtml::activeDropDownList($model, 'parent_id', $sections, array('encode'=>false,'class'=>'form-control', $model->title =>array('selected' => 'selected'))) . '<br>';;
                            ?>
                        </div>

                        <!-- порядок нельзя редактировать из этой формы, т.к. можно просто перетащить в дереве -->
                        <!--<label class="control-label col-md-3"> <?php # echo CHtml::activeLabel($model, 'order'); ?> </label>
                        <div class="col-md-9">
                            <?php  # echo CHtml::activeTextField($model,'order', array('class'=>'form-control touchspin_retries')) . '<br>'; ?>
                        </div>-->

                        <label class="control-label col-md-3"> <?php echo CHtml::activeLabel($model, 'icon'); ?> </label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php  echo "<i class=" . "'" . $model['icon'] . "'" . "></i>" ?>
                                </span>
                                    <?php   echo  CHtml::activeTextField($model,'icon', array('class' => 'form-control')) . '<br>'; ?>
                            </div>
                        </div>

                        <label class="control-label col-md-3" style="padding-top:20px;"> <?php echo CHtml::activeLabel($model, 'is_visible'); ?> </label>
                        <div class="col-md-9" style="padding-top:20px;">
                            <div class="input-group">
                                <span class="input-group-addon" style="padding:0px; margin:0px;">
                                    <a id="toggleVisible" class="btn btn-default btn-sm">Да/Нет</a>
                                </span>
                                <?php  echo CHtml::activeTextField($model,'is_visible', array('class' => 'form-control','id' => 'visibleInput')) . '<br>'; ?>
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
                            <?php echo CHtml::button('Отменить', array('class' => 'btn default','onclick' => 'js:document.location.href="/menueditor/menulinks/' . '"')); ?>
                            <?php echo CHtml::button('Удалить', array('class' => 'btn red','onclick' => 'js:document.location.href="/menueditor/menulinks/delete/id/' . $model['id'] . '"')); ?>
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
                                    echo "<li style = 'padding-top:5px;'>";
                                    echo CHtml::link($subsection['title'], array(MenuLinks::model()->getModelName() . '/update/id/' . $subsection['id']), array('class' => 'btn default btn-xs', 'style' => ' color: #2980b9;'));
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

    <script>

        $( document ).ready(function() {
    var visibleInput = document.getElementById('visibleInput');
            if(visibleInput.value == 1)
            {
                visibleInput.value = "Да";
            }
            else if(!visibleInput.value)
            {
               visibleInput.value = "Нет";
            }
            
    });
    </script>



    <script>

        var toggleVisible = document.getElementById('toggleVisible');
        var visibleInput = document.getElementById('visibleInput');
        toggleVisible.onclick = function()
        {
            if(visibleInput.value == "Да")
            {
                visibleInput.value = "Нет";
            }
            else 
            {
                visibleInput.value = "Да";
            }
            
            return false;
        }

    </script>
