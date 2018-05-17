<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Pear;

/**
 * Mail class
 *
 * Helper class to send emails
 */
class Mail
{
    /**
     * @var string
     */
    protected $host;

    /**
     * Failure messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Returns the host attribute
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns an array with all failure messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets host attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setHost($value)
    {
        return $this->host = $value;
    }

    /**
     * Constructor
     *
     * @throws RuntimeException
     *
     * @param array $options
     */
    public function __construct()
    {
        @include_once 'System.php';

        if (!class_exists('System', false))
            throw new \RuntimeException("PEAR is not installed in your system!");

        include_once 'Net/SMTP.php';

        if (!class_exists('\Net_SMTP', false))
            throw new \RuntimeException("PEAR::Net_SMTP is not installed!");

        @include_once 'Mail.php';

        if (!class_exists('\Mail', false))
            throw new \RuntimeException("PEAR::Mail is not installed!");
    }

    /**
     * Adds an error
     *
     * @param string $code
     * @param string $message
     *
     * @return null
     */
    protected function error($code, $message = null)
    {
        if (!array_key_exists($code, $this->errors))
            $this->errors[$message] = (is_null($message) && array_key_exists($code, $this->messagesTemplates)) ? $this->messagesTemplates[$code] : $message;
    }

    /**
     * Sends an email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     *
     * @return boolean
     */
    public function send($from, $to, $subject, $body)
    {
        $headers = array (
            'From'         => $from,
            'To'           => $to,
            'Subject'      => $subject,
            'Content-type' => 'text/html;charset=iso-8859-1'
        );

        $smtp = \Mail::factory(
            'smtp',
            array ('host' => $this->host, 'auth' => false)
        );

        $mail = $smtp->send($to, $headers, $body);

        if (\PEAR::isError($mail))
        {
            $this->error(
            	$mail->getCode(), $mail->getMessage()
            );

            return false;
        }

        return true;
    }
}