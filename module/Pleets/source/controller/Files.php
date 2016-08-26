<?php

namespace Pleets\Controller;

Use Drone\Mvc\AbstractionController;
Use Drone\FileSystem\Shell;

class Files extends AbstractionController
{
    public function fileActions()
    {
        $return_data = array();
        $this->setTerminal(true);

        $file = 'module/'.$_GET["module"]."/source/".$_GET["type"]."/".$_GET["file"];

        if ($_GET["type"] == 'view')
            $file = 'module/'.$_GET["module"]."/source/".$_GET["type"]."/".$_GET["controller"].'/'.$_GET["file"];

        if (file_exists($file))
        {
            $return_data['file'] = $file;
            $return_data['description'] = $this->describeFile($file);
            $return_data['module'] = $_GET["module"];
            $return_data['type'] = $_GET["type"];

            if ($_GET["type"] == 'view')
                $return_data['controller'] = $_GET["controller"];
        }

        return $return_data;
    }

    public function editFile()
    {
        $return_data = array();

        $file = 'module/'.$_GET["module"]."/source/".$_GET["type"]."/".$_GET["file"];

        if ($_GET["type"] == 'view')
            $file = 'module/'.$_GET["module"]."/source/".$_GET["type"]."/".$_GET["controller"].'/'.$_GET["file"];

        if (file_exists($file))
        {
            $return_data['file'] = $file;
            $return_data['description'] = $this->describeFile($file);
            $return_data['module'] = $_GET["module"];
            $return_data['type'] = $_GET["type"];

            if ($_GET["type"] == 'view')
                $return_data['controller'] = $_GET["controller"];
        }

        return $return_data;
    }

	public function saveFile()
	{
		$return_data = array();
        $this->setTerminal(true);

        $return_data["success"] = false;

        $file = $_POST["file"];
        $contents = $_POST["file-edition"];

        if (file_exists($file))
    		$return_data["success"] = !(file_put_contents($file, $contents) === false);

		return $return_data;
	}

    private function describeFile($_file)
    {
        $file = fopen ($_file, "r");

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
            'size' => $this->fileSize($_file)
        );
    }

    private function fileSize($file)
    {
        return number_format(filesize($file) / 1024, 2) . " KB";
    }
}