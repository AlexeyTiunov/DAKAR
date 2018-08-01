<?php
/**
 * General transport.
 *
 * @package Capall_MailTransport
 */

/**
 * General transport.
 *
 * @internal
 *
 * @author Alexey Shockov <alexey@shockov.com>
 * @package Capall_MailTransport
 */
class Capall_MailTransport_Sender
{
    /**
     * @var Net_SMTP
     */
    private $_transport;
    /**
     * @param Net_SMTP $transport
     */
    public function __construct($transport)
    {
        $this->_transport = $transport;
    }
    /**
     * @see CEvent::HandleEvent()
     * @see bxmail()
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $additionalHeaders
     */
    public function send($to, $subject, $message, $additionalHeaders = '')
    {
        preg_match('/From: (.+)\n/i', $additionalHeaders, $matches);
        list(, $from) = $matches;

        if (PEAR::isError($settingResult = $this->_transport->mailFrom(trim($from)))) {
            throw new Capall_MailTransportException($settingResult);
        }

        // $to string may contains many recipients.
        $to = split(',', $to);
        foreach ($to as $recipient) {
            $recipient = trim($recipient);

            if (!empty($recipient)) {
                if (PEAR::isError($settingResult = $this->_transport->rcptTo($recipient))) {
                    throw new Capall_MailTransportException($settingResult);
                }
            }
        }

        $eol = CAllEvent::GetMailEOL();

        $additionalHeaders .= $eol . 'To: ' . $to;
        $additionalHeaders .= $eol . 'Subject: ' . $subject;

        if (PEAR::isError($sendingResult = $this->_transport->data($additionalHeaders . "\r\n\r\n" . $message))) {
            throw new Capall_MailTransportException($sendingResult);
        }
    }
}
