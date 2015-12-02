<?php

class MenuLinks extends EMongoDocument
{
    public $id;
    public $title;
    public $controller;
    public $is_visible;
    public $url;
    public $icon;
    public $parent_id;
    public $order = 1;
    public $menu_id = 7;
    public $page_id;
    public $not_user_role;
    public $lang;
    public $user_role;


    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getMenuName()
    {
        return 'Горизонтальное меню';
    }

   
    public function getCollectionName()
    {
        return 'menu_links';
    }

    public function getModelName()
    {
        return 'menulinks';
    }

    public function rules()
    {
        return array(
            array(
                'title',
                'required'
            ),
            array(
                'id, order, parent_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'is_visible',
                'boolean'
            ),
            array(
                'id',
                'length',
                'max' => 11
            ),
            array(
                'id,  is_visible',
                'safe',
                'on' => 'search'
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Название',
            'controller' => 'Контроллер',
            'is_visible' => 'Видимость',
            'icon' => 'Иконка',
            'parent_id' => 'Родитель',
            'order' => 'Порядок',
            'url' => 'Ссылка',
            'menu_id' => 'ID меню',
            'page_id' => '',
            'not_user_role' => '',
            'lang' => '',
            'user_role' => '',
        );
    }

/*
    protected  function beforeValidate()
    {
        //if (($this->address == '') && ($this->post_processing != '') && ($this->device == 0) && ($this->formula == 0) )
        if ( ($this->device == 0) && ($this->formula > 0) )
        {
            $temp = $this->formulasCalc();
            $this->formula_name = $temp[$this->formula];
        }

        if ( ($this->device == 0) && ($this->formula == 0) )
        {
            $this->formula_name = $this->post_processing;
        }


        return true;
    }
*/

   /**
    * Метод для приведения типов перед сохранением в MongoDB
    */  
    protected function beforeSave()
    {
        if(parent::beforeSave())
        {

            $arrayType = $this->returnArrayType();
            foreach ($arrayType as $name => $type) 
            {
                switch ($type) {
                    case 'integer':
                        $this->$name = intval($this->$name);
                        break;
                    case 'float':
                        $this->$name = floatval($this->$name);
                        break;
                    case 'boolean':
                        $this->$name = (bool)$this->$name;
                        break;
                }
            }
        
            return true;
        } 
        else 
        {
            return false;
        }
    }

   
   /**
    * Метод возвращает массив типов переменных
    */
    protected function returnArrayType()
    {
        $type = 'string';
        $namesEl = array();
        
        // проверяем какой тип должен быть
        // если есть numerical и integerOnly значит тип int
        // если есть numerical и нет integerOnly, значит тип float
        $rules = $this->rules();
        //получем массив с названиями полей модели
        foreach($rules as $rule)
        {
            $names = $rule[0];
            $names_array = explode(',', $names);
            foreach ($names_array as $nameOne)
            {
                $nameOne = trim($nameOne);

                if (in_array('numerical', $rule) )
                {
                    $namesEl[$nameOne] = 'float';

                    if (in_array('integerOnly', $rule))
                    {
                        $namesEl[$nameOne] = 'integer';
                    }
                }
                else if(in_array('boolean', $rule))
                {
                    $namesEl[$nameOne] = 'boolean';
                }
                else if(($namesEl[$nameOne] != 'integer') && (($namesEl[$nameOne] != 'float') ) && (($namesEl[$nameOne] != 'boolean')))
                {
                    $namesEl[$nameOne] = 'string';
                }
            }
        }
        return $namesEl;
    }


   /**
    * Метод возвращает новый id для пункта меню
    */
    public static function getNewId(){
        $idAll = array();
        // получаем значения всех Id
        $all = MenuLinks::model()->findAll();
        
        foreach ($all as $one)
        {
            $idAll[]=$one["id"];
        }
       
        rsort($idAll);
        $max = array_shift($idAll);
 
        $idNew = ++$max;

        return $idNew;
    }

    protected function afterSave()
    {


    }

    protected function afterDelete()
    {
    
    }
}