<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Response
 *
 * @package framework
 **/
class Sonata_Response
{
  /**
   * Array with all HTTP status codes and descriptions
   *
   * @var array
   **/
  static protected $statusTexts = array(
    '100' => 'Continue',
    '101' => 'Switching Protocols',
  	'200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',
    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '306' => '(Unused)',
    '307' => 'Temporary Redirect',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '402' => 'Payment Required',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Timeout',
    '409' => 'Conflict',
    '410' => 'Gone',
    '411' => 'Length Required',
    '412' => 'Precondition Failed',
    '413' => 'Request Entity Too Large',
    '414' => 'Request-URI Too Long',
    '415' => 'Unsupported Media Type',
    '416' => 'Requested Range Not Satisfiable',
    '417' => 'Expectation Failed',
    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Timeout',
    '505' => 'HTTP Version Not Supported',
  );
  
  /**
   * Array with all supported mime types
   *
   * @var array
   */
  static protected $mimeTypes = array(
    'html' => 'text/html',
    'xml'  => 'text/xml',
    'json' => 'application/json',
    'rss'  => 'application/rss+xml',
    'atom' => 'application/atom+xml',
  );
  
  /**
   * The response's mime type
   *
   * @var string
   */
  protected $mimeType = 'text/xml';
  
  /**
   * The response's format
   *
   * @var string
   */
  protected $format = 'xml';
  
  /**
   * The HTTP status code
   *
   * @var integer
   **/
  protected $statusCode = 200;
  
  /**
   * The HTTP status text
   *
   * @var string
   **/
  protected $statusText = 'OK';
  
  /**
   * Array with all headers
   *
   * @var array
   */
  protected $headers = array();
  
  /**
   * The body of the HTTP response
   *
   * @var string
   */
  protected $body = null;
  
  /**
   * Registers a new mime type and its format
   *
   * @param string $format The format
   * @param string $mimeType The mime type
   */
  static public function registerMimeType($format, $mimeType)
  {
    $keys = array_keys(self::$mimeTypes);
    if (!in_array($format, $keys) && is_string($mimeType))
    {
      self::$mimeTypes[$format] = $mimeType;
    }
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // Actually does nothing ...
  }
  
  /**
   * Setter status code (with optional status text)
   *
   * @param integer $code The status code
   * @param string $name The status text
   */
  public function setStatusCode($code, $name = null)
  {
    $this->statusCode = $code;
    $this->statusText = $name !== null ? $name : self::$statusTexts[$code];
  }
  
  /**
   * Getter status code
   *
   * @return integer The status code
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
  
  /**
   * Getter status text
   *
   * @return string The status text
   */
  public function getStatusText()
  {
    return $this->statusText;
  }
  
  /**
   * Add a header to the headers array
   *
   * @param string $name The header's name
   * @param string $value The header's value
   */
  public function addHeader($name, $value)
  {
    $this->headers[$name] = $value;
  }
  
  /**
   * Getter headers array
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  
  /**
   * Setter format
   *
   * @param string $format The format
   */
  public function setFormat($format)
  {
    $keys = array_keys(self::$mimeTypes);
    if (in_array($format, $keys))
    {
      $this->format = $format;
      $this->mimeType = self::$mimeTypes[$format];
    }
  }
  
  /**
   * Getter format
   *
   * @return string The format
   */
  public function getFormat()
  {
    return $this->format;
  }
  
  /**
   * Getter mime type
   *
   * @return string The mime type
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  
  /**
   * Appends data to the response's body
   *
   * @param string $data The data to be append
   */
  public function appendToBody($data)
  {
    $this->body .= $data;
  }
  
  /**
   * Getter body
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  
  /**
   * Flushes the response
   */
  public function flush()
  {
    header('HTTP/1.0 '.$this->statusCode);
    header('Content-type: '.$this->mimeType);
    
    foreach ($this->headers as $name => $value) 
    {
      header(sprintf('%s: %s', $name, $value));
    }
    
    print $this->body;
    $this->headers = array();
    $this->data = null;
  }
}
