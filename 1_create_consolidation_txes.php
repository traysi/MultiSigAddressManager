#!/usr/bin/php
<?php
// Copyright (c) 2019 Traysi Hylian, MinerMore.com
// Distributed under the MIT software license, see the accompanying
// file LICENSE or http://www.opensource.org/licenses/mit-license.php.

include("rpc.php");
include("config.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (! $config['multisig']) { die("You must edit the config.php file first.\n"); }

$rpc = new Bitcoin($config['rpcuser'],$config['rpcpassword'],$config['rpcbind'],$config['rpcport']);

$multisig = $config['multisig'];

$address[] = $multisig;

$coins = $rpc->listunspent(1,99999999,$address);

if(! is_array($coins)) die("Error: unable to connect to the coin daemon.");

$txouts = array();

$x = 0;
$y = 0;

if(is_array($coins)) {
  while (list($k,$v)=each($coins)) {
    if ($v['confirmations'] < $config['confirmations']) continue;
    if ($v['amount'] > $config['consolidate_amount']) continue;
    // if (! $v['spendable']) continue; // This part would work if it was your own single-sig wallet address

    if ($x % $config['utxo_limit'] == 0) $y++;
    $x++;
    $o['txid'] = $v['txid'];
    $o['vout'] = $v['vout'];
    $o['scriptPubKey'] = $config['scriptPubKey'];
    $o['redeemScript'] = $config['redeemScript'];

    $txouts[$y][] = $o;
    $sum[$y] = $sum[$y] + $v['amount'];
  }
}

if(is_array($txouts)) {
  while (list($y,$v)=each($txouts)) {
    $recipient[$multisig] = $sum[$y] - $config['tx_fee'];
    if (count($v) == $config['utxo_limit']) {
      $tx = $rpc->createrawtransaction($v,$recipient);
      if (! $tx) {
        print "Missed tx: amount: $sum[$y] ($recipient[$multisig]\n";
        print_r($v);
        print_r($rpc);
      }
      print "$tx\n";
    }
  }
}

