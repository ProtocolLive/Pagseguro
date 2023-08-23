<?php

namespace ProtocolLive\Pagseguro;

/**
 * @version 2023.06.27
 */
final class Entrega{
  /**
   * @param $Rua Rua do endereço
   * @param $Numero Número do endereço
   * @param $Complemento Complemento do endereço
   * @param $Bairro Bairro do endereço
   * @param $Cidade Cidade do endereço
   * @param $Estado Código do Estado do endereço. Padrão ISO 3166-2
   * @param $Pais Pais do endereço
   * @param $Cep CEP do endereço
  * @link https://dev.pagseguro.uol.com.br/reference/create-order
   */
  public function __construct(
    public string $Rua,
    public string $Numero,
    public string $Complemento,
    public string $Bairro,
    public string $Cidade,
    public string $Estado,
    public string $Pais,
    public string $Cep
  ){}

  public function Get():array{
    return [
      'street' => $this->Rua,
      'number' => $this->Numero,
      'complement' => $this->Complemento,
      'locality' => $this->Bairro,
      'city' => $this->Cidade,
      'region_code' => $this->Estado,
      'country' => $this->Pais,
      'postal_code' => $this->Cep
    ];
  }
}