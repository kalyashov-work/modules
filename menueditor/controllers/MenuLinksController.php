<?php

class MenuLinksController extends BaseController
{

    public $layout = '//layouts/empty';

    public static function actionsTitles()
    {
        return array(
            'Index'  => 'Список всех пунктов меню',
            'Update' => 'Редактирование пунтка меню',
            'Create' => 'Создание пункта меню',
            'Delete' => 'Удаление пункта меню',
            'ChangeParent' => 'Изменение родителя (при перетаскивании в дереве)',
            'Rename' => 'Переименование пункта меню',
            'ChangeOrder' => 'Изменение порядка пункта меню'
        );
    }


    public function getSubSections($id = NULL)
    {

        $childs = MenuLink::model()->findAll(
            array(
                'conditions'=>array(
                    'menu_id'=>array('equals'=>7)
                ),
                'sort'=>array('parent_id' => 1, 'order' => 1)
            ));


        foreach ($childs as $child)
        {
            $show = false;
            $allowed_modules = explode (',', $child->is_visible);
   
            foreach($allowed_modules as $am)
            {
                if (trim($am) == 1)
                    $show = true;
                else
                {
                    if (Yii::app()->params[trim($am)]) $show = true;
                }
            }

            if ($show)
            {
                $temp = array();
                $temp['id'] = $child->id;
                $temp['title'] = $child->title;
                if ($child->parent_id > 0) 
                    $filtered_childs[$child->parent_id][] = $temp;
            }
        }

        if($id)
        {
            return $filtered_childs[$id];
        }

        return $filtered_childs;
    }

    public function getMainSections()
    {
        return Menu::model()->findByAttributes(array('name' => MenuLinks::model()->getMenuName()))->getSections();
    }

    public function getInvisibleSections()
    {
        return MenuLink::model()->findAll(
            array('conditions'=>
                array('menu_id'=>array('equals'=>7),
                      'is_visible'=>array('equals'=>false)),
                'sort'=>array('parent_id' => 1, 'order' => 1)));
    }

	public function actionIndex()
	{

        if(Yii::app()->request->isAjaxRequest)
        {
            $this->layout = '//layouts/empty';
            Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;
        }
        else
        {
            $this->layout = '//layouts/main';
        }

        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/jstree/dist/jstree.min.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootbox/bootbox.min.js', CClientScript::POS_END);


        $modelAttributes = Menulinks::model()->attributeLabels();

        $sections = $this->getMainSections();
        $invisibleSections = $this->getInvisibleSections();
        $subsections = $this->getSubSections();

        $this->render('index', array(
            'attributes'=>$modelAttributes,
            'sections' => $sections,
            'subsections' => $subsections,
            'invisibleSections' => $invisibleSections,
        ));
        
	}


    public function actionCreate()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $this->layout = '//layouts/empty';
        }
        else
        {
            $this->layout = '//layouts/main';
        }

        Yii::app()->clientScript->registerCSSFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-switch/static/stylesheets/bootstrap-switch-metro.css');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-switch/static/js/bootstrap-switch.min.js', CClientScript::POS_BEGIN);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-touchspin/bootstrap.touchspin.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScript('initTouchSpin', "FormComponents.initTouchSpin();", CClientScript::POS_END);

        $model = new MenuLinks;

        $sections = $this->getMainSections();

        $parents[0] = 'Нет родителя';
        foreach ($sections as $section) 
        {
            $parents[$section['id']] = $section['title'];
        }
        
        
        if(isset($_POST['MenuLinks']))
        {
            
            $model->attributes = $_POST['MenuLinks'];

            // TODO поправить баг
            $model->controller = $_POST['MenuLinks']['controller'];
            $model->url = $_POST['MenuLinks']['url'];
            $model->icon = $_POST['MenuLinks']['icon'];
            
            if($model->save())
            {  
                $this->redirect(array('index'));
            }
        }
        
        $this->render('create', array(
            'model' => $model,
            'sections' => $parents,
            ));
    }

    public function actionUpdate($id)
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $this->layout = '//layouts/empty';
        }
        else
        {
            $this->layout = '//layouts/main';
        }

        Yii::app()->clientScript->registerCSSFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-switch/static/stylesheets/bootstrap-switch-metro.css');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-switch/static/js/bootstrap-switch.min.js', CClientScript::POS_BEGIN);
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->getBaseUrl(false).'/js/plugins/bootstrap-touchspin/bootstrap.touchspin.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScript('initTouchSpin', "FormComponents.initTouchSpin();", CClientScript::POS_END);

        $sections = $this->getMainSections();

        $parents[0] = 'Нет родителя';
        foreach ($sections as $section) 
        {
            if($section['id'] != $id)
                $parents[$section['id']] = $section['title'];
        }

        $childs = MenuLink::model()->findAll(
            array(
                'conditions'=>array(
                    'menu_id'=>array('equals'=>7)
                ),
                'sort'=>array('parent_id' => 1, 'order' => 1)
            ));


        foreach ($childs as $child)
        {
            $show = false;
            $allowed_modules = explode (',', $child->is_visible);
   
            foreach($allowed_modules as $am)
            {
                if (trim($am) == 1)
                    $show = true;
                else
                {
                    if (Yii::app()->params[trim($am)]) $show = true;
                }
            }

            if ($show)
            {
                $temp = array();
                $temp['id'] = $child->id;
                $temp['title'] = $child->title;
                if ($child->parent_id > 0) 
                    $filtered_childs[$child->parent_id][] = $temp;
            }
        }

        $model = $this->loadModel($id);

        if(isset($_POST['MenuLinks']))
        {
            $model->attributes = $_POST['MenuLinks'];

            // TODO поправить баг
            $model->controller = $_POST['MenuLinks']['controller'];
            $model->url = $_POST['MenuLinks']['url'];
            $model->icon = $_POST['MenuLinks']['icon'];
            
            if($model->save())
            {   
                $this->redirect(array('index'));
            }
        }

        $updateSection = null;
        foreach ($sections as $section) {
            if($section->id == $id)
                $updateSection = $section;
        }

        $this->render('update', array(
            'model' =>$model,
            'sections' => $parents,
            'section' => $updateSection,
            'subsections' => $filtered_childs[$id],
            ));

    }

    public function actionDelete($id)
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $this->layout = '//layouts/empty';
        }
        else
        {
            $this->layout = '//layouts/main';
        }

        $this->loadModel($id)->delete();
        $this->redirect(array('index'));
    }

    public function actionChangeParent($id,$newId)
    {
        if(($id == null) || ($newId == null)) 
            return;
        $model = $this->loadModel($id);
        
        $model->parent_id = (int)$newId;
        
        if ($model->save()) 
            echo 'saved';
    }

    public function actionRename($id,$newTitle)
    {
        if(($id == null)) 
            return;

        $model = $this->loadModel($id);

        $model->title = $newTitle;

        if($model->save())
            echo "saved";
    }


    public function actionChangeOrder()
    {

        if(isset($_POST['data']))
        {
            $nodes = json_decode($_POST['data']);

            foreach($nodes as $node)
            {
                $model = $this->loadModel($node->id);
                $model->order = $node->order;
                $model->save();
            }
        }
    }

    
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return The loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
        $model = Menulinks::model()->findAllByAttributes(array('id'=>(int) $id));
       
        if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

        return $model[0];
	}


}
