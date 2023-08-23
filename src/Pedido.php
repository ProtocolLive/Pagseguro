<?php

namespace ProtocolLive\Pagseguro;
use DomainException;

/**
 * @version 2023.06.27
 */
final class Pedido{
  private array $Params = [];
  private int $Total = 0;
  private int $TotalCartoes = 0;

  /**
   * @link https://dev.pagseguro.uol.com.br/reference/pay-order-with-token
   */
  public function __construct(
    private Pagseguro $Pagseguro,
    public int $Id,
    public string $Nome,
    public string $Email,
    public string $Cpf,
    public Entrega|null $Entrega = null,
    public string|null $UrlNotificacao = null
  ){
    $this->Params = [
      'reference_id' => $Id,
      'customer' => [
        'name' => $Nome,
        'email' => $Email,
        'tax_id' => $Cpf
      ]
    ];
    if($Entrega !== null):
      $this->Params['shipping']['address'] = $Entrega->Get();
    endif;
    if($UrlNotificacao !== null):
      $this->Params['notification_urls'] = $UrlNotificacao;
    endif;
  }

  /**
   * @param string $Numero Número do cartão de crédito ou cartão de débito.
   * @param int $Mes Mês de expiração do cartão de crédito, cartão de débito ou token de rede.
   * @param int $Ano Ano de expiração do cartão de crédito, cartão de débito ou token de rede.
   * @param int $Valor Valor a ser cobrado em centavos. Apenas números inteiros positivos.
   * @param string $Codigo Código de Segurança do cartão de crédito, cartão de débito ou token de rede.
   * @param string $Nome Nome do portador do cartão de crédito, cartão de débito e token de rede.
   * @param int $Parcelas Quantidade de parcelas. Obrigatório para o método de pagamento cartão de crédito.
   * @param string|null $Descricao Descrição da cobrança.
   * @param bool $PreAutorizar Parâmetro que indica se uma transação de cartão de crédito deve ser apenas pré-autorizada (reserva o valor da cobrança no cartão do cliente por até 5 dias) ou se a transação deve ser capturada automaticamente (cobrança realizada em apenas um passo). Obrigatório para o método de pagamento cartão de crédito. Função indisponível para o método de pagamento cartão de débito e token de rede (débito).
   * @param string|null $CartaoNoPagseguro Identificador PagSeguro do cartão de crédito salvo (Cartão Tokenizado pelo PagSeguro). Função indisponível para o método de pagamento cartão de débito e token de rede.
   * @param bool $SalvarNoPagseguro Indica se o cartão deverá ser armazenado no PagSeguro para futuras compras. Informe true para que seja armazenado, na resposta da requisição você terá o token do cartão em payment_method.card.id. Função indisponível para o método de pagamento cartão de débito e token de rede.
   * @link https://dev.pagseguro.uol.com.br/reference/charge-object
   */
  public function CartaoAdd(
    int $Valor,
    string $Codigo,
    string $Nome,
    string $Numero = null,
    int $Mes = null,
    int $Ano = null,
    string $CartaoNoPagseguro = null,
    int $Parcelas = 1,
    string|null $Descricao = null,
    bool $PreAutorizar = false,
    bool $SalvarNoPagseguro = false
  ):void{
    $this->TotalCartoes += $Valor;
    $this->Params['charges'][] = [
      'reference_id' => $this->Id,
      'amount' => [
        'value' => $Valor,
        'currency' => 'BRL'
      ],
      'payment_method' => [
        'type' => 'CREDIT_CARD',
        'installments' => $Parcelas,
        'capture' => ! $PreAutorizar,
        'card' => [
          'security_code' => $Codigo,
          'holder' => [
            'name' => $Nome
          ]
        ]
      ]
    ];
    $count = count($this->Params['charges']) - 1;
    if($CartaoNoPagseguro !== null):
      $this->Params['charges'][$count]['payment_method']['card']['id'] = $CartaoNoPagseguro;
    else:
      $this->Params['charges'][$count]['payment_method']['card']['number'] = $Numero;
      $this->Params['charges'][$count]['payment_method']['card']['exp_month'] = $Mes;
      $this->Params['charges'][$count]['payment_method']['card']['exp_year'] = $Ano;
    endif;
    if($Descricao !== null):
      $this->Params['charges'][$count]['description'] = $Descricao;
    endif;
    if($SalvarNoPagseguro):
      $this->Params['charges'][$count]['payment_method']['card']['store'] = true;
    endif;
  }

  public function Enviar():array{
    if($this->Total < 500):
      throw new DomainException('Total deve ser maior que R$ 5,00');
    endif;
    if($this->TotalCartoes !== $this->Total):
      throw new DomainException('O valor cobrado é diferente do total dos itens');
    endif;
    return $this->Pagseguro->Curl(Metodos::Pedido, $this->Params);
  }

  public function ItemAdd(
    string $Nome,
    int $Quantidade,
    int $Preco
  ):void{
    $this->Params['items'][] = [
      'name' => $Nome,
      'quantity' => $Quantidade,
      'unit_amount' => $Preco
    ];
    $this->Total += $Quantidade * $Preco;
  }

  public function TelefoneAdd(
    Telefone $Telefone
  ):void{
    $this->Params['customer']['phones'][] = $Telefone;
  }
}