<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Network;

/**
 * Http class
 *
 * Helper class to send http headers
 */
class Http
{
    /**
     * Status codes
     * As per http://php.net/manual/en/function.header.php, See the » HTTP/1.1 specification
     *
     * @var integer
     * @link http://www.faqs.org/rfcs/rfc2616.html
     */
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * Status codes and their respective description
     *
     * @var array
     */
    protected $httpStatusCodes = [
        self::HTTP_CONTINUE                        => 'Continue',
        self::HTTP_SWITCHING_PROTOCOLS             => 'Switching Protocols',
        self::HTTP_OK                              => 'OK',
        self::HTTP_CREATED                         => 'Created',
        self::HTTP_ACCEPTED                        => 'Accepted',
        self::HTTP_NON_AUTHORITATIVE_INFORMATION   => 'Non-Authoritative Information',
        self::HTTP_NO_CONTENT                      => 'No Content',
        self::HTTP_RESET_CONTENT                   => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT                 => 'Partial Content',
        self::HTTP_MULTIPLE_CHOICES                => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY               => 'Moved Permanently',
        self::HTTP_FOUND                           => 'Found',
        self::HTTP_SEE_OTHER                       => 'See Other',
        self::HTTP_NOT_MODIFIED                    => 'Not Modified',
        self::HTTP_USE_PROXY                       => 'Use Proxy',
        self::HTTP_TEMPORARY_REDIRECT              => 'Temporary Redirect',
        self::HTTP_BAD_REQUEST                     => 'Bad Request',
        self::HTTP_UNAUTHORIZED                    => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED                => 'Payment Required',
        self::HTTP_FORBIDDEN                       => 'Forbidden',
        self::HTTP_NOT_FOUND                       => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED              => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE                  => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED   => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT                 => 'Request Time-out',
        self::HTTP_CONFLICT                        => 'Conflict',
        self::HTTP_GONE                            => 'Gone',
        self::HTTP_LENGTH_REQUIRED                 => 'Length Required',
        self::HTTP_PRECONDITION_FAILED             => 'Precondition Failed',
        self::HTTP_REQUEST_ENTITY_TOO_LARGE        => 'Request Entity Too Large',
        self::HTTP_REQUEST_URI_TOO_LONG            => 'Request-URI Too Large',
        self::HTTP_UNSUPPORTED_MEDIA_TYPE          => 'Unsupported Media Type',
        self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
        self::HTTP_EXPECTATION_FAILED              => 'Expectation Failed',
        self::HTTP_INTERNAL_SERVER_ERROR           => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED                 => 'Not Implemented',
        self::HTTP_BAD_GATEWAY                     => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE             => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT                 => 'Gateway Time-out',
        self::HTTP_VERSION_NOT_SUPPORTED           => 'HTTP Version not supported'
    ];

    /**
     * Gets the HTTP Status description from the code
     *
     * @param integer $code
     *
     * @throws LogicException
     *
     * @return string
     */
    public function getStatusText($code)
    {
        $codes = $this->httpStatusCodes;

        if (!in_array($code, array_keys($codes)))
            throw new \LogicException("Status code not supported");

        return $this->httpStatusCodes[$code];
    }

    /**
     * Sets the HTTP Status Header
     *
     * @param integer $code
     *
     * @return string
     */
    public function writeStatus($code)
    {
        $description = $this->getStatusText($code);
        $header = $_SERVER['SERVER_PROTOCOL'] . " $code $description";

        header($header);

        return $header;
    }
}