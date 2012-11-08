<?php
namespace Barberry\Plugin\OpenOffice;

use Barberry\Plugin;
use Barberry\Direction;
use Barberry\Monitor;
use Barberry\ContentType;

class Installer implements Plugin\InterfaceInstaller
{
    /**
     * @var string
     */
    private $tempDirectory;

    public function __construct($tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    public function install(Direction\ComposerInterface $directionComposer, Monitor\ComposerInterface $monitorComposer,
        $pluginParams = array())
    {
        foreach (self::directions() as $pair) {
            $directionComposer->writeClassDeclaration(
                $pair[0],
                eval('return ' . $pair[1] . ';'),
                <<<PHP
new Plugin\\OpenOffice\\Converter ($pair[1], '{$this->tempDirectory}');
PHP
            );
        }

        $monitorComposer->writeClassDeclaration('OpenOffice', "parent::__construct('{$this->tempDirectory}')");
    }

//--------------------------------------------------------------------------------------------------

    private static function directions()
    {
        return array(
            array(ContentType::odt(), '\Barberry\ContentType::doc()'),
            array(ContentType::odt(), '\Barberry\ContentType::pdf()'),
            array(ContentType::ots(), '\Barberry\ContentType::xls()'),
            array(ContentType::ods(), '\Barberry\ContentType::xls()'),
            array(ContentType::ott(), '\Barberry\ContentType::doc()'),
            array(ContentType::ott(), '\Barberry\ContentType::pdf()'),
        );
    }

}