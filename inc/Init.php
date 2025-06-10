<?php
/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf;

class Init 
{
    /**
     * Store all the classes inside an array
     * @return array full of classes
     */
    public static function get_services()
    {
        return [
            Pages\MainPages::class,
            Base\Enqueue::class,
            Features\Idx\Engine::class
        ];
    }
    /**
     * Loop through the classes, initialize them
     * and call the register method if it exist
     * @return void
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }
    /**
     * Initialize the class
     * @param class $class from the services array
     * @return class instance new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class;
        return $service;
    }
}