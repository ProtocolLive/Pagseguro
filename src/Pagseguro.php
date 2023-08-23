<?php

namespace ProtocolLive\Pagseguro;

/**
 * @version 2023.06.27
 */
final class Pagseguro{
  public function __construct(
    public readonly string $Token,
    public string $DirLogs,
    public bool $Sandbox = false,
    public bool $Logs = true
  ){}

  public function Curl(
    Metodos $Metodo,
    array $Params
  ):array{
    $url = 'api.pagseguro.com/' . $Metodo->value;
    if($this->Sandbox):
      $url = 'sandbox.' . $url;
    endif;
    $url = 'https://' . $url;
    $curl = curl_init($url);
    curl_setopt_array($curl, [
      CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($Params),
      CURLOPT_HTTPHEADER => [
        'content-type: application/json',
        'Authorization: ' . $this->Token,
      ],
      CURLOPT_VERBOSE => $this->Logs,
      CURLOPT_STDERR => fopen($this->DirLogs . '/curl.log', 'a')
    ]);
    if($this->Logs):
      $log = date('Y-m-d H:i:s') . ' ' . microtime(true) . PHP_EOL;
      $log .= $url . PHP_EOL;
      $log .= json_encode($Params, JSON_PRETTY_PRINT) . PHP_EOL;
      file_put_contents($this->DirLogs . '/send.log', $log, FILE_APPEND);
    endif;
    $return = curl_exec($curl);
    if($this->Logs):
      file_put_contents($this->DirLogs . '/send.log', $return . PHP_EOL, FILE_APPEND);
    endif;
    return json_decode($return, true);
  }
}