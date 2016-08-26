<?php

namespace Api\Controller;

use Drone\Mvc\AbstractionController;
use Drone\FileSystem\Shell;

class Index extends AbstractionController
{
	public function index()
	{
        $data = array();

        $shell = new Shell();
        $files = $shell->ls('module', true);

        $parse_data = array();

        foreach ($files as $file)
        {
            $partes = explode("/", $file);

            if (strstr($file, '.') == '.php' && basename($file) != 'Module.php')
                $parse_data[] = substr($file, strlen("module/"), strlen($file));
        }

        $file_data = array();
        $nms_data = array();

        foreach ($parse_data as $row)
        {
            $nms = explode("/", $row);
            $parsed_nms = "";

            $i = 0;
            foreach ($nms as $part)
            {
                $i++;

                if ($i != 2)
                    $parsed_nms .= "/" . $part;
            }

            $file_data[] = "data/api" . $parsed_nms;
            $nms_data[] = substr($parsed_nms, 1, strlen($parsed_nms));
        }

        $fq_nms_data = array();

        foreach ($nms_data as $nms)
        {
            $array_nms = explode("/", $nms);

            if (!array_key_exists($array_nms[0], $fq_nms_data))
                $fq_nms_data[$array_nms[0]] = array();

            if (!array_key_exists($array_nms[1], $fq_nms_data[$array_nms[0]]))
                $fq_nms_data[$array_nms[0]][$array_nms[1]] = array();

            $fq_nms_data[$array_nms[0]][$array_nms[1]][] = basename($nms);
        }

        $data["clases"] = $fq_nms_data;

		return $data;
	}

    public function getFileInfo()
    {
        $data = array();
        $this->setTerminal(true);

        $file = $_GET["file"];

        $json_file = "data/api/" . $file . ".json";

        if (!file_exists($json_file))
            throw new \Exception ("No data!");

        $file_info = $this->object_to_array(json_decode(file_get_contents($json_file, true)));

        $data["file_info"] = $file_info;
        $data["file_info"]["fq_class"] = $file;

        return $data;
    }

    private function object_to_array($obj)
    {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->object_to_array($val);
            }
        }
        else $new = $obj;

        return $new;
    }
}