<?php

class TestTreeController extends BaseController
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
            'Tree' => 'Древовидная структура из пунктов меню',
            'ChangeOrder' => 'Изменить порядок',
            'Test' => 'Test'
        );
    }



    public function getSubNodesByParent($parentId)
    {
        return TestTree::model()->findAll(
            array(
                'conditions'=>array(
                    'id'=>array('greater'=> 0),
                    'parent_id' => array('equals' => intval($parentId))
                ),
            ));
    }

     public function getSubNodes()
        {

            return TestTree::model()->findAll(
                array(
                    'conditions'=>array(
                        'parent_id'=>array('greater'=> 0)
                    ),
                    'sort'=>array('order' => 1)
                ));
        }

        public function getMainNodes()
        {
            return TestTree::model()->findAll(
            array(
                'conditions'=>array(
                    'parent_id'=>array('equals'=>0)
                ),
                'sort'=>array('order' => 1)
            ));
        }

    public function actionTest()
    {
            $subNodes = $this->getSubNodesByParent(2);
            foreach($subNodes as $node)
            {
                echo $node->title;
            }
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

        $this->render('index');
	}


    public function actionTree()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            $this->layout = '//layouts/empty';
        }
        else
        {
            $this->layout = '//layouts/main';
        }

        $this->render('tree',array(
            'sections' => $this->getMainSections(),
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

        $model = new TestTree;
        $nodes = $this->getMainNodes();

        $parents[0] = 'Нет родителя';
        foreach ($nodes as $node) 
        {
            $parents[$node->id] = $node->title;
        }
      
        if(isset($_POST['TestTree']))
        {

            $model->attributes = $_POST['TestTree'];
        
            if($model->save())
            {  
                $this->redirect(array('index')); 
            }
        }
        
        $this->render('create', array(
            'model' => $model,
            'nodes' => $parents,
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

        
        $model = $this->loadModel($id);

        if(isset($_POST['TestTree']))
        {
            $model->attributes = $_POST['TestTree'];

            if($model->save())
            {   
                $this->redirect(array('index'));
            }
        }

        $nodes = $this->getMainNodes();

        $parents[0] = 'Нет родителя';
        foreach ($nodes as $node) 
        {
            $parents[$node->id] = $node->title;
        }


        $this->render('update', array(
            'model' => $model,
            'nodes' => $parents,
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
        $model = TestTree::model()->findAllByAttributes(array('id'=>(int) $id));
       
        if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

        return $model[0];
	}


}
