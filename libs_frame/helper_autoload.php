<?php
require_once __DIR__ . '/helper_defines.php';

final class SyFrameLoader {
    /**
     * @var \SyFrameLoader
     */
    private static $instance = null;
    /**
     * @var array
     */
    private $preHandleMap = [];
    /**
     * swift mailer未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $swiftMailerStatus = true;
    /**
     * smarty未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $smartyStatus = true;
    /**
     * @var array
     */
    private $smartyRootClasses = [];
    /**
     * fpdf未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $fpdfStatus = true;
    /**
     * excel未初始化标识 true：未初始化 false：已初始化
     * @var bool
     */
    private $excelStatus = true;

    private function __construct() {
        $this->preHandleMap = [
            'FPdf' => 'preHandleFPdf',
            'Twig' => 'preHandleTwig',
            'Swift' => 'preHandleSwift',
            'Resque' => 'preHandleResque',
            'Smarty' => 'preHandleSmarty',
            'SmartyBC' => 'preHandleSmarty',
            'PHPExcel' => 'preHandlePhpExcel',
        ];

        $this->smartyRootClasses = [
            'smarty' => 'smarty.php',
            'smartybc' => 'smartybc.php',
        ];
    }

    private function __clone() {
    }

    /**
     * @return \SyFrameLoader
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function preHandleFPdf(string $className) : string {
        if($this->fpdfStatus){
            define('FPDF_VERSION', '1.81');
            $this->fpdfStatus = false;
        }

        return SY_FRAME_LIBS_ROOT . $className . '.php';
    }

    private function preHandleTwig(string $className) : string {
        return SY_FRAME_LIBS_ROOT . 'Template/' . str_replace('_', '/', $className) . '.php';
    }

    private function preHandleSwift(string $className) : string {
        if($this->swiftMailerStatus){ //加载swift mailer依赖文件
            require_once SY_FRAME_LIBS_ROOT . 'Mailer/Swift/depends/cache_deps.php';
            require_once SY_FRAME_LIBS_ROOT . 'Mailer/Swift/depends/mime_deps.php';
            require_once SY_FRAME_LIBS_ROOT . 'Mailer/Swift/depends/message_deps.php';
            require_once SY_FRAME_LIBS_ROOT . 'Mailer/Swift/depends/transport_deps.php';
            require_once SY_FRAME_LIBS_ROOT . 'Mailer/Swift/depends/preferences.php';

            $this->swiftMailerStatus = false;
        }

        return SY_FRAME_LIBS_ROOT . 'Mailer/' . str_replace('_', '/', $className) . '.php';
    }

    private function preHandleResque(string $className) : string {
        return SY_FRAME_LIBS_ROOT . 'Queue/' . str_replace('_', '/', $className) . '.php';
    }

    private function preHandleSmarty(string $className) : string {
        if ($this->smartyStatus) {
            $smartyLibDir = SY_FRAME_LIBS_ROOT . 'Template/Smarty/libs/';
            define('SMARTY_DIR', $smartyLibDir);
            define('SMARTY_SYSPLUGINS_DIR', $smartyLibDir . '/sysplugins/');
            define('SMARTY_RESOURCE_CHAR_SET', 'UTF-8');

            $this->smartyStatus = false;
        }

        $lowerClassName = strtolower($className);
        if(isset($this->smartyRootClasses[$lowerClassName])){
            return SMARTY_DIR . $this->smartyRootClasses[$lowerClassName];
        } else {
            return SMARTY_SYSPLUGINS_DIR . $lowerClassName . '.php';
        }
    }

    private function preHandlePhpExcel(string $className) : string {
        if($this->excelStatus){
            define('PHPEXCEL_ROOT', SY_FRAME_LIBS_ROOT . 'Excel/');

            $this->excelStatus = false;
        }

        return SY_FRAME_LIBS_ROOT . 'Excel/' . str_replace('_', '/', $className) . '.php';
    }

    /**
     * 加载文件
     * @param string $className 类名
     * @return bool
     */
    public function loadFile(string $className) : bool {
        $nameArr = explode('/', $className);
        $funcName = $this->preHandleMap[$nameArr[0]] ?? null;
        if(is_null($funcName)){
            $nameArr = explode('_', $className);
            $funcName = $this->preHandleMap[$nameArr[0]] ?? null;
        }

        $file = is_null($funcName) ? SY_FRAME_LIBS_ROOT . $className . '.php' : $this->$funcName($className);
        if(is_file($file) && is_readable($file)){
            require_once $file;
            return true;
        }

        return false;
    }
}

final class SyProjectLoader {
    /**
     * @var \SyProjectLoader
     */
    private static $instance = null;

    private function __construct(){
    }

    /**
     * @return \SyProjectLoader
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 加载文件
     * @param string $className 类名
     * @return bool
     */
    public function loadFile(string $className) : bool {
        $file = SY_PROJECT_LIBS_ROOT . $className . '.php';
        if(is_file($file) && is_readable($file)){
            require_once $file;
            return true;
        }

        return false;
    }
}

/**
 * 基础公共类自动加载
 * @param string $className 类全名
 * @return bool
 */
function syFrameAutoload(string $className) {
    $trueName = str_replace([
        '\\',
        "\0",
    ], [
        '/',
        '',
    ], $className);
    return SyFrameLoader::getInstance()->loadFile($trueName);
}

/**
 * 项目公共类自动加载
 * @param string $className 类全名
 * @return bool
 */
function syProjectAutoload(string $className) {
    $trueName = str_replace([
        '\\',
        "\0",
    ], [
        '/',
        '',
    ], $className);
    return SyProjectLoader::getInstance()->loadFile($trueName);
}

spl_autoload_register('syFrameAutoload');
spl_autoload_register('syProjectAutoload');