<?php

class TestTree extends EMongoDocument
{
    public $id;
    public $title;
    public $parent_id;
    public $order;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function getCollectionName()
    {
        return 'test_tree';
    }

    public function getModelName()
    {
        return 'testtree';
    }

    public function rules()
    {
        return array(
            array(
                'title,order',
                'required'
            ),
            array(
                'id, order, parent_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'id',
                'length',
                'max' => 11
            ),
            array(
                'id',
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
            'parent_id' => 'Родитель',
            'order' => 'Порядок',
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
    // метод для приведения типов для mongoDB
    // перед сохранением
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

   
    // метод возвращает массив типов переменных
    protected function returnArrayType(){
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
                else if(($namesEl[$nameOne] != 'integer') && (($namesEl[$nameOne] != 'float') ) && (($namesEl[$nameOne] != 'boolean') ))
                {
                    $namesEl[$nameOne] = 'string';
                }
                
            }
        }

        return $namesEl;
    }

    // метод для генерации нового id устройства
    // считывается последний id и и выдается id+1
    // если он уникальный
    public static function getNewId(){
        $idAll = array();
        // получаем значения всех Id
        $all = TestTree::model()->findAll();
        
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