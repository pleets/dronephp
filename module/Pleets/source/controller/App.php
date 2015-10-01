<?php

namespace Pleets\Controller;

Use Pleets\Mvc\AbstractionController;
Use Pleets\FileSystem\Shell;

class App extends AbstractionController
{
	public function index()
	{
		$shell = new Shell();
		$modules = $shell->ls('module');

		$parsed_modules = array();
		foreach ($modules as $module)
		{
			if (!in_array($module, array('.','..')))
				$parsed_modules[] = $module;
		}

		$mods = array();
		foreach ($parsed_modules as $module) 
		{
			$mods[$module] = array();
			

			/* Get controllers */
			$controllers = $shell->ls('module/'.$module.'/source/controller');
		
			$parsed_controllers = array();
			foreach ($controllers as $ctrl) 
			{
				if (!in_array($ctrl, array('.','..'))) 
				{
					$desc = $this->describeFile('module/'.$module.'/source/controller/'.$ctrl);

					$parsed_controllers[] = array(
						'name' => $ctrl,
						'lines' => $desc["lines"],
						'characters' => $desc["characters"],
					);
				}
			}

			/* Get models */
			$models = $shell->ls('module/'.$module.'/source/model');

			$parsed_models = array();
			foreach ($models as $model) 
			{
				if (!in_array($model, array('.','..'))) 
				{
					$desc = $this->describeFile('module/'.$module.'/source/model/'.$model);

					$parsed_models[] = array(
						'name' => $model,
						'lines' => $desc["lines"],
						'characters' => $desc["characters"],
					);
				}
			}


			/* Get views */
			$views_for_controllers = array();
			foreach ($parsed_controllers as $ctrl) 
			{
				$views = $shell->ls('module/'.$module.'/source/view/'.strstr($ctrl['name'], '.', true) );

				$parsed_views = array();
				foreach ($views as $view) 
				{
					if (!in_array($view, array('.','..'))) 
					{
						$desc = $this->describeFile('module/'.$module.'/source/view/'.strstr($ctrl['name'], '.', true).'/'.$view);

						$parsed_views[] = array(
							'name' => $view,
							'lines' => $desc["lines"],
							'characters' => $desc["characters"],
						);
					}
				}

				$views_for_controllers[strstr($ctrl['name'], '.', true)] = $parsed_views;
			}


			$mods[$module]['controller'] = $parsed_controllers;
			$mods[$module]['model'] = $parsed_models;
			$mods[$module]['view'] = $views_for_controllers;
		}

		return array("modules" => $mods);
	}

    private function describeFile($file)
    {
        $file = fopen ($file, "r");

        $num_lines = 0;
        $characters = 0;

        while (!feof ($file)) {
            if ($line = fgets($file)){
               $num_lines++;
               $characters += strlen($line);
            }
        }
        fclose ($file);

        return array(
            'lines' => $num_lines,
            'characters' => $characters,
        );
    }

    public function getViewsFromControler()
    {
        $return_data = array();
        $this->setTerminal(true);

        $file = 'module/'.$_POST["module"]."/source/view/".$_POST["controller"];

		$shell = new Shell();
		$vistas = $shell->ls($file);

		$parsed_views = array();
		foreach ($vistas as $vista)
		{
			if (!in_array($vista, array('.', '..')))
				$parsed_views[] = $vista;
		}

        $return_data["views"] = $parsed_views;
        $return_data["module"] = $_POST["module"];
        $return_data["controller"] = $_POST["controller"];

        return $return_data;    	
    }
}