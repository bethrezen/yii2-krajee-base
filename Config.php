<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-krajee-base
 * @version 1.3.0
 */

namespace kartik\base;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Global configuration helper class for Krajee extensions
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Config
{
    const VENDOR_NAME = "kartik-v/";
    const NAMESPACE_PREFIX = "\\kartik\\";
    const DEFAULT_REASON = "for your selected functionality"; 
    
    protected static $_validHtmlInputs = [
        'hiddenInput',
        'textInput',
        'passwordInput',
        'textArea',
        'checkbox',
        'radio',
        'listBox',
        'dropDownList',
        'checkboxList',
        'radioList',
        'input',
        'fileInput'
    ];
    
    protected static $_validDropdownInputs = [
        'listBox',
        'dropDownList',
        'checkboxList',
        'radioList'
    ];
    
    protected static $_validInputWidgets = [
        '\kartik\typeahead\Typeahead' => ['yii2-widgets', 'yii2-widget-typeahead'],
        '\kartik\select2\Select2' => ['yii2-widgets', 'yii2-widget-select2'],
        '\kartik\depdrop\DepDrop' => ['yii2-widgets', 'yii2-widget-depdrop'],
        '\kartik\touchspin\TouchSpin' => ['yii2-widgets', 'yii2-widget-touchspin'],
        '\kartik\switchinput\SwitchInput' => ['yii2-widgets', 'yii2-widget-switchinput'],
        '\kartik\rating\StarRating' => ['yii2-widgets', 'yii2-widget-rating'],
        '\kartik\rating\FileInput' => ['yii2-widgets', 'yii2-widget-fileinput'],
        '\kartik\range\RangeInput' => ['yii2-widgets', 'yii2-widget-rangeinput'],
        '\kartik\color\ColorInput' => ['yii2-widgets', 'yii2-widget-colorinput'],
        '\kartik\date\DatePicker' => ['yii2-widgets', 'yii2-widget-datepicker'],
        '\kartik\time\TimePicker' => ['yii2-widgets', 'yii2-widget-timepicker'],
        '\kartik\datetime\DateTimePicker' => ['yii2-widgets', 'yii2-widget-datetimepicker'],
        '\kartik\daterange\DateRangePicker' => 'yii2-daterange',
        '\kartik\sortinput\SortableInput' => 'yii2-sortinput',
        '\kartik\money\MaskMoney' => 'yii2-money',
        '\kartik\checkbox\CheckboxX' => 'yii2-checkbox',
        '\kartik\slider\Slider' => 'yii2-slider',
    ];

    /**
     * Validate a single extension dependency
     * @param string name the extension class name (without vendor namespace prefix)
     * @param string|array repo the extension package repository names (without vendor name prefix)
     * @param string reason a user friendly message for dependency validation failure
     * @throws InvalidConfigException if extension fails dependency validation
     */
    public static function checkDependency($name = '', $repo = '', $reason = self::DEFAULT_REASON)
    {
        if (empty($name)) {
            return;
        }
        $command = "php composer.phar require " . self::VENDOR_NAME;
        $version = ": \"@dev\"";
        $class = (substr($name, 0, 8) == self::NAMESPACE_PREFIX) ? $name : self::NAMESPACE_PREFIX . $name;

        if (is_array($repo)) {
            $repos = "one of '" . implode("' OR '", $repo) . "' extensions. ";
            $installs = $command . implode("{$version}\n\n--- OR ---\n\n" . $command, $repo) . $version;
        } else {
            $repos = "the '" . $repo . "' extension. ";
            $installs = $command . $repo . $version;
        }

        if (!class_exists($class)) {
            throw new InvalidConfigException("The class '{$class}' was not found and is required {$reason}.\n\n" .
                "Please ensure you have installed " . $repos .
                "To install, you can run this console command from your application root:\n\n" .
                $installs);
        }
    }

    /**
     * Validate multiple extension dependencies
     * @param array extensions the configuration of extensions with each array
     * item setup as required in `checkDependency` method. The following keys
     * can be setup:
     * - name: string, the extension class name (without vendor namespace prefix)
     * - repo: string,  the extension package repository name (without vendor name prefix)
     * - reason: string,  a user friendly message for dependency validation failure
     * @throws InvalidConfigException if extension fails dependency validation
     */
    public static function checkDependencies($extensions = [])
    {
        foreach ($extensions as $extension) {
            $name = empty($extension[0]) ? '' : $extension[0];
            $repo = empty($extension[1]) ? '' : $extension[1];
            $reason = empty($extension[2]) ? '' : self::DEFAULT_REASON;
            self::checkDependency($name, $repo, $reason);
        }
    }

    /**
     * Gets list of namespaced Krajee input widget classes as an associative
     * array, where the array keys are the namespaced classes, and the array
     * values are the names of the github repository to which these classes
     * belong to.
     * @returns array
     */
    public static function getInputWidgets()
    {
        return self::$_validInputWidgets;
    }

    /**
     * Check if a type of input is any possible valid input (html or widget)
     * @param string $type the type of input
     * @returns boolean
     */
    public static function isValidInput($type)
    {
        return self::isHtmlInput($type) || self::isInputWidget($type) || $type === 'widget';
    }
    
    /**
     * Check if a type of input is a valid input widget
     * @param string $type the type of input
     * @returns boolean
     */
    public static function isInputWidget($type)
    {
        return isset(self::$_validInputWidgets[$type]);
    }
    
    /**
     * Check if a input type is a valid Html Input
     * @param string $type the type of input
     * @returns boolean
     */
    public static function isHtmlInput($type)
    {
        return in_array($type, self::$_validHtmlInputs);
    }

    /**
     * Check if a input type is a valid dropdown input
     * @param string $type the type of input
     * @returns boolean
     */
    public static function isDropdownInput($type)
    {
        return in_array($type, self::$_validDropdownInputs);
    }
    
    /**
     * Check if a namespaced widget is valid or installed.
     * @throws InvalidConfigException
     */
    public static function validateInputWidget($type, $reason = self::DEFAULT_REASON)
    {
        if (self::isInputWidget($type)) {
            self::checkDependency($type, self::$_validInputWidgets[$type], $reason);
        }
    }
    
    /**
     * Convert a language string in yii\i18n format to 
     * a ISO-639 format (2 or 3 letter code).
     * @param string $language the input language string
     * @return string
     */
    public static function getLang($language) {
        $pos = strpos($language, "-");
        return $pos > 0 ? substr($language, 0, $pos) : $language;
    }
}
