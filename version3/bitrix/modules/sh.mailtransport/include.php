<?php
/**
 * custom_mail for Bitrix.
 */

// Some system functionality for Bitrix (mailtransport::registerTransport()).
require_once dirname(__FILE__).'/install/index.php';

if (!@include_once 'Net/SMTP.php') {
	// TODO Log unavailable dependency issue...
	return;
}

require_once dirname(__FILE__).'/classes/Capall/MailTransportException.php';
require_once dirname(__FILE__).'/classes/Capall/MailTransport/Sender.php';

// TODO Custom mail handler already exists! Log issue.
if (!function_exists('custom_mail')) {
    /**
     * @see CEvent::HandleEvent()
     * @see bxmail()
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $additionalHeaders Additional headers setted by Bitrix.
     *
     * @return bool
     */
    function custom_mail($to, $subject, $message, $additionalHeaders = '')
    {
        // Cache to send many mails in one script run.
        static $transport, $sender;

        try {
            if (!$sender) {
                if (!$transport) {
                    $host     = COption::GetOptionString('sh.mailtransport', 'host');
                    if (COption::GetOptionInt('sh.mailtransport', 'ssl')) {
                        $host = 'ssl://'.$host;
                    }
                    $port     = COption::GetOptionInt('sh.mailtransport', 'port');
                    $user     = COption::GetOptionString('sh.mailtransport', 'username');
                    $password = COption::GetOptionString('sh.mailtransport', 'password');

                    $transport = new Net_SMTP($host, $port);

                    if (PEAR::isError($connectionResult = $transport->connect())) {
                        throw new Capall_MailTransportException($connectionResult);
                    }
                    if (PEAR::isError($authenticationResult = $transport->auth($user, $password))) {
                        throw new Capall_MailTransportException($authenticationResult);
                    }
                }

                $sender = new Capall_MailTransport_Sender($transport);
            }

            $sender->send($to, $subject, $message, $additionalHeaders);

            return true;
        } catch (Capall_MailTransportException $error) {
            CEventLog::Log(
                'WARNING',
                'MAILTRANSPORT_ERROR',
                'sh.mailtransport',
                null, // TODO Or try to get event identifier to here?
                (string)$error
            );

            return false;
        } catch (Exception $error) {
            // Unknown error...

            return false;
        }
    }
}
