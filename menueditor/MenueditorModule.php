<?php

class MenuEditorModule extends WebModule
{	
	public static $active = true;


    public static function name()
    {
        return 'Редактор меню';
    }


    public static function description()
    {
        return 'Модуль редактора меню';
    }


    public static function version()
    {
        return '1.0';
    }


	public function init()
	{

	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
        {
            return false;
        }
	}


}
