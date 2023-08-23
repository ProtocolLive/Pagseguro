<?php

namespace ProtocolLive\Pagseguro;

/**
 * @version 2023.06.27
 */
final class Telefone{
  /**
   * @param int $Pais Código de operadora do País (DDI)
   * @param int $Estado Código de operadora local (DDD)
   * @param int $Numero Número de telefone
   * @param TelefoneTipo $Tipo Indica o tipo de telefone
   * @link https://dev.pagseguro.uol.com.br/reference/phone-object
   */
  public function __construct(
    public int $Pais,
    public int $Estado,
    public int $Numero,
    public TelefoneTipo $Tipo
  ){}

  public function Get():array{
    return [
      'country' => $this->Pais,
      'area' => $this->Estado,
      'number' => $this->Numero,
      'type' => $this->Tipo->value
    ];
  }
}