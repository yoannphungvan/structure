<?php
namespace PROJECT\Models\Managers;
use Silex\Application;
class AppProxy {
    /**
     * @var Singleton
     * @access private
     * @static
     */
    private static $_instance = null;
    /**
     * Application instance
     *
     * @var null|\Silex\Application
     */
    private $app = null;
    /**
     * Private constructor that init the $app
     *
     * @param void
     * @return void
     */
    private function __construct(Application $app = null){
        $this->app = $app;
    }
    /**
     * Return the $app instance
     *
     * @return null|Application
     */
    public function getApp()
    {
        return $this->app;
    }
    /**
     * Return a static instance of the AppProxy
     *
     * @param void
     * @return AppProxy
     */
    public static function getInstance(Application $app = null)
    {
        if (is_null(self::$_instance) && $app !== null) {
            self::$_instance = new AppProxy($app);
        } elseif (is_null(self::$_instance) && $app === null) {
            // Both are null and this should never happen
            throw new \Exception("AppProxy isn't initialized");
        }
        return self::$_instance;
    }
}