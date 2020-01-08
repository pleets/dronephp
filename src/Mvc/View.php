<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Mvc;

/**
 * View class
 *
 * This class manages views and parameters
 */
class View
{
    use \Drone\Util\ParamTrait;

    /**
     * Regex for identifiers
     *
     * @var string
     */
    const IDENTIFIER_REGEX = '[_a-zA-Z][_a-zA-Z0-9]*[.]?[_a-zA-Z0-9]+';

    /**
     * View name
     *
     * @var string
     */
    protected $name;

    /**
     * The path where views are located.
     *
     * @var string
     */
    protected $path;

    /**
     * The path rendered views are located
     *
     * @var string
     */
    protected $cache;

    /**
     * Returns the view name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the cache path
     *
     * @return string
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the view name
     *
     * @param string
     * @param mixed $name
     *
     * @return null
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the path
     *
     * @param string
     * @param mixed $path
     *
     * @return string
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Sets the cache path
     *
     * @param string
     * @param mixed $cache
     *
     * @return string
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct($name, array $parameters = [])
    {
        $this->name  = $name;
        $this->setParams($parameters);
    }

    /**
     * Gets the contents of view
     *
     *
     * @param null|mixed $file
     * @throws Exception\ViewNotFoundException
     * @return  string
     */
    public function getContents($file = null)
    {
        $_view = $this->path . DIRECTORY_SEPARATOR .
            str_replace('.', DIRECTORY_SEPARATOR, !is_null($file) ? $file : $this->name) . '.phtml';

        $contents = file_get_contents($_view);

        if ($contents === false) {
            throw new Exception\ViewGetContentsErrorException("The view '" .$this->name. "' does not exists");
        }

        return $contents;
    }

    /**
     * Loads the view
     *
     * @return  null
     */
    public function render()
    {
        if (count($this->getParams())) {
            extract($this->getParams(), EXTR_SKIP);
        }

        $viewFile = $this->getFile();
        $view = file_get_contents($viewFile);

        if (preg_match("/@extends\('(" .self::IDENTIFIER_REGEX. ")'\)/", $view, $matches) === 1) {
            $extendsStm = array_shift($matches);
            $viewIdentifier = array_shift($matches);

            $view = str_replace($extendsStm, file_get_contents($this->getFile($viewIdentifier)), $view);

            while (preg_match("/@section\('(" .self::IDENTIFIER_REGEX. ")'\)/", $view, $sectionOpening) === 1) {
                $sectionStm = array_shift($sectionOpening);
                $sectionIdentifier = array_shift($sectionOpening);
                $sectionFirstIndex = strpos($view, $sectionStm);

                if (preg_match("/@endsection\('(" . $sectionIdentifier . ")'\)/", $view, $sectionEnding) !== 1) {
                    // no ending statment
                    $view = str_replace($sectionStm, '', $view);
                    continue;
                }

                $endsectionStm        = array_shift($sectionEnding);
                $endsectionIdentifier = array_shift($sectionEnding);
                $endsectionFirstIndex = strpos($view, $endsectionStm);

                $sectionContent = substr(
                    $view,
                    $sectionFirstIndex,
                    $endsectionFirstIndex - $sectionFirstIndex + strlen($endsectionStm)
                );

                $sectionContentTrimmed = str_replace($sectionStm, '', $sectionContent);
                $sectionContentTrimmed = str_replace($endsectionStm, '', $sectionContentTrimmed);

                $view = str_replace($sectionContent, '', $view);
                $view = str_replace("@yield('$sectionIdentifier')", $sectionContentTrimmed, $view);
            }
        }

        // replace no match yields
        while (preg_match("/@yield\('(" .self::IDENTIFIER_REGEX. ")'\)/", $view, $matches) === 1) {
            $view = preg_replace("/@yield\('(" .self::IDENTIFIER_REGEX. ")'\)/", '', $view);
        }

        $uid = uniqid() . time();

        file_put_contents($this->cache . DIRECTORY_SEPARATOR . $uid, $view);
        include $this->cache . DIRECTORY_SEPARATOR . $uid;
        unlink($this->cache . DIRECTORY_SEPARATOR . $uid);
    }

    /**
     * Gets the file path
     *
     * @param string $file
     *
     * @return  string
     */
    private function getFile($file = null)
    {
        $_view = $this->path . DIRECTORY_SEPARATOR .
            str_replace('.', DIRECTORY_SEPARATOR, !is_null($file) ? $file : $this->name) . '.phtml';

        if (!file_exists($_view)) {
            throw new Exception\ViewNotFoundException("The view '" .$this->name. "' does not exists");
        }

        return $_view;
    }
}
