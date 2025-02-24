<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit24e41bbaee1332276f826502c7093d78
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPMB_Toolkit\\Includes\\' => 22,
            'WPMB_Toolkit\\Common\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPMB_Toolkit\\Includes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'WPMB_Toolkit\\Common\\' => 
        array (
            0 => __DIR__ . '/../..' . '/common',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WPMB_Toolkit\\Common\\Constants' => __DIR__ . '/../..' . '/common/Constants.php',
        'WPMB_Toolkit\\Common\\Helpers' => __DIR__ . '/../..' . '/common/Helpers.php',
        'WPMB_Toolkit\\Common\\Hooks' => __DIR__ . '/../..' . '/common/Hooks.php',
        'WPMB_Toolkit\\Includes\\Options\\MainOptionsPage' => __DIR__ . '/../..' . '/includes/Options/MainOptionsPage.php',
        'WPMB_Toolkit\\Includes\\Options\\OptionsValidation' => __DIR__ . '/../..' . '/includes/Options/OptionsValidation.php',
        'WPMB_Toolkit\\Includes\\Tools\\Logger\\Logger' => __DIR__ . '/../..' . '/includes/Tools/Logger/Logger.php',
        'WPMB_Toolkit\\Includes\\Tools\\Logger\\LoggerTrait' => __DIR__ . '/../..' . '/includes/Tools/Logger/LoggerTrait.php',
        'WPMB_Toolkit\\Includes\\Tools\\ToolsManager' => __DIR__ . '/../..' . '/includes/Tools/ToolsManager.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit24e41bbaee1332276f826502c7093d78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit24e41bbaee1332276f826502c7093d78::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit24e41bbaee1332276f826502c7093d78::$classMap;

        }, null, ClassLoader::class);
    }
}
