<?php

/**
 * @package PhemriseWpCf
 */

namespace PhemriseIct\PhemriseWpCf\Base;

class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();  
    }
}
