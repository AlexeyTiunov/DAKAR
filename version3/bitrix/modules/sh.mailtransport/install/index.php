<?php
/**
 * Module descriptor (and installer).
 */

if (class_exists('sh_mailtransport')) {
    return;
}

/**
 * Module descriptor for Bitrix.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class sh_mailtransport extends CModule
{
    // Fail.
    var $MODULE_ID = "sh.mailtransport";

    public $MODULE_VERSION      = '1.1.0';
    public $MODULE_VERSION_DATE = '2011-01-10 15:35:14';

    public $PARTNER_NAME;
    public $PARTNER_URI;

    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    /**
     *
     */
    public function __construct()
    {
        // Magic... Don't works, if in top of file.
        IncludeModuleLangFile(__FILE__);

        $this->MODULE_NAME        = GetMessage('MAILTRANSPORT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MAILTRANSPORT_MODULE_DESCRIPTION');

        // Fail.
        $this->PARTNER_NAME = "Alexey Shockov";
        $this->PARTNER_URI  = "http://alexey.shockov.com/";
    }
    /**
     * Registration.
     */
    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        // Register to observe any event, that fired before sending mail.
        RegisterModuleDependences(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            __CLASS__,
            'registerTransport'
        );
        RegisterModuleDependences(
            'main',
            'OnEventLogGetAuditTypes',
            $this->MODULE_ID,
            __CLASS__,
            'getEventLogAuditTypes'
        );
    }
    /**
     * Unregistration.
     */
    public function DoUninstall()
    {
        UnRegisterModuleDependences(
            'main',
            'OnEventLogGetAuditTypes',
            $this->MODULE_ID,
            __CLASS__,
            'getEventLogAuditTypes'
        );
        UnRegisterModuleDependences(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            __CLASS__,
            'registerTransport'
        );

        UnRegisterModule($this->MODULE_ID);
    }
    /**
     * Audit types for Bitrix event log.
     *
     * @return array
     */
    public static function getEventLogAuditTypes()
    {
        $errorIdentifier = 'MAILTRANSPORT_ERROR';

        return array(
            $errorIdentifier => '['.$errorIdentifier.'] '.GetMessage($errorIdentifier),
        );
    }
    /**
     * Empty method, only for callback (module already included).
     */
    public static function registerTransport()
    {

    }
}
