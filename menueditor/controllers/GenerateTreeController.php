<?php

class GenerateTreeController extends BaseController
{

    public $layout = '//layouts/empty';

    public static function actionsTitles()
    {
        return array(
            'Index'  => 'Список всех пунктов меню',
            'ChangeOrder' => 'Изменение сортировки',
            'Generate' => 'Генерация json',
            'GenerateTest' => 'Генерация тест',
            
            
        
        );
    }

    /*TEST*/
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
    /*TEST*/


    public function getSubSections($id = NULL)
    {

        $childs = MenuLink::model()->findAll(
            array(
                'conditions'=>array(
                    'menu_id'=>array('equals'=>7)
                ),
                'sort'=>array('order' => 1)
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
                $temp['parent_id'] = $child->parent_id;
                $temp['order'] = $child->order;
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

    public function actionChangeOrder($id = null)
    {
        if(($id == null)) 
            $this->redirect(array('index'));


        if(isset($_POST['data']))
        {
            
            $nodes = json_decode($_POST['data']);

            /*foreach ($nodes as $node) {
                echo $node->id;
            }*/

            $model = $this->loadModel($id);
            $model->title += 'h';

            if ($model->save()) 
                echo 'saved';
        }

    }

    public function actionGenerate()
    {
        $sectionData = array();
        $subsectionData = array();

        $sections = $this->getMainSections();
        $subsections = $this->getSubSections();

        foreach($sections as $section)
        {
            $sectionData[] = array(
                    'id' => $section->id,
                    'text' => $section->title,
                    'parent' => '#',
                    'class' =>'jstree-drop',
                    'data' => array('order' => $section->order),
                );
            
            foreach($subsections[$section['id']] as $subsection)
            {
                 $subsectionData[] = array
                 (
                    'id' => $subsection['id'],
                    'text' => $subsection['title'],
                    'parent' => $subsection['parent_id'],
                    'class' =>'jstree-drop',
                    'data' => array('order' => $subsection['order']),
                 );
            }
        }

       
        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode(array_merge($sectionData,$subsectionData));
    }
   

    public function actionGenerateTest()
    {
        $nodesData = array();
        $subnodesData = array();

        $nodes = $this->getMainNodes();
        $subnodes = $this->getSubNodes();

        foreach ($nodes as $node) 
        {
            $nodesData[] = array(
                    'id' => $node->id,
                    'text' => $node->title,
                    'parent' => '#',
                    'class' =>'jstree-drop',
                    'data' => array('order' => $node->order),
                );
        }

        foreach ($subnodes as $node) 
        {
            $subnodesData[] = array(
                'id' => $node->id,
                'text' => $node->title,
                'parent' => $node->parent_id,
                'class' => 'jstree-drop',
                'data' => array('order' => $node->order),
            );
        }


        header('Content-type: text/json');
        header('Content-type: application/json');
    
        echo json_encode(array_merge($nodesData,$subnodesData));
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
