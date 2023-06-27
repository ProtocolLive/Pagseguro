<?php

use ProtocolLive\Pagseguro\Pagseguro;
use ProtocolLive\Pagseguro\Pedido;

require(dirname(__DIR__) . '/src/Pagseguro.php');
require(dirname(__DIR__) . '/src/Pedido.php');
require(dirname(__DIR__) . '/src/Metodos.php');

$ps = new Pagseguro(
  '587569515915',
  __DIR__,
  true
);

$pedido = new Pedido(
  $ps,
  1,
  'teste',
  'a@a.com',
  '98365986000'
);
$pedido->ItemAdd('Pastel de carne', 2, 1000);
$pedido->CartaoAdd(
  500,
  '123',
  'teste',
  '4111111111111111',
  12,
  2026
);
var_dump($pedido->Enviar());