<?php

class GenerateTreeController extends BaseController
{

    public $layout = '//layouts/empty';

    public static function actionsTitles()
    {
        return array(
            'Generate' => 'Генерация json',
        );
    }

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

    public function getHiddenSections()
    {
        return MenuLink::model()->findAll(
            array('conditions'=>
                array('menu_id'=>array('equals'=>7),
                      'is_visible'=>array('equals'=>false)),
                'sort'=>array('order' => 1)));
    }


    public function actionGenerate()
    {
        $sectionData = array();
        $subsectionData = array();
        $hiddenSectionsData = array();

        $sections = $this->getMainSections();
        $subsections = $this->getSubSections();
        $hiddenSections = $this->getHiddenSections();

        foreach($sections as $section)
        {
            $sectionData[] = array(
                    'id' => $section->id,
                    'text' => $section->title,
                    'parent' => (!$section->parent_id) ? '#' : $section->parent_id,,
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

        foreach($hiddenSections as $section)
        {
            $hiddenSectionsData[] = array(
                    'id' => $section->id,
                    'text' => '<span class="hidden_menu_link">'.$section->title.'</span>',
                    'parent' => (!$section->parent_id) ? '#' : $section->parent_id,
                    'class' =>'jstree-drop',
                    'type' => 'file',
                    'data' => array('order' => $section->order),
                );
        }

        $sectionData = array_merge($sectionData,$hiddenSectionsData,$subsectionData);

        usort($sectionData, function($a, $b){
            return ($a['data']['order'] - $b['data']['order']);
        });

        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode(array_merge($sectionData));
    }
   

}
