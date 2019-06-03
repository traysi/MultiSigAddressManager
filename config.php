<?php

// RPC credentials, set in your ~/.coin/coin.conf file
$config['rpcuser'] = 'pgnuser';
$config['rpcpassword'] = 'pgnpw';
$config['rpcbind'] = '127.0.0.1';
$config['rpcport'] = '8756';

// When consolidating, how many transactions to consolidate at once?
$config['utxo_limit'] = 50;

// How much do you want to pay for each transaction you broadcast?
$config['tx_fee'] = '0.01';

// Only consolidate amounts up to this amount:
$config['consolidate_amount'] = 12500;

// Confirmations for maturity
$config['confirmations'] = 100;

// **********************************************************
// Default config below is for the Pigeoncoin multisig wallet. 
// **********************************************************

// The public receive address of your multisig wallet
$config['multisig'] = 'rQG3D3nzy3jfFxugbmUoZ9LhjpeJ4vrYbR';

// The scriptPubKey for that address. 
// Find this with coin-cli validateaddress MULTISIG_RCV_ADDRESS
$config['scriptPubKey'] = 'a914c5b873ef1c19fb34081d9d96953d3ce6888e951387';

// The redeemScript for this multisig wallet. You got it when you created the
// address. If you don't have it, well, there are ways to find it but that's
// beyond the scope of this utility.
$config['redeemScript'] = '542103691c82bc0eb33ecaa70dd98159d64d080427033ebeccb1a1023e64df387a60492103f3059538472efa230cf2032054a52c089de740d59b8db73a1cc1298d5a61726e2102351f708aa8b32c681f251add687cedab7c9da2bd0f41f8325493a09fcf4ca2fc21035f4fbe1cd72c787753afb59a6dc0273ca7db0c76d2170d4d245fa7e991127c9f2102cf97c1cbcff277f2c0692f55c9c35c39446976b3e8df719082df9e1780eec5eb41044745320206c566b112c3c7b8d5459f1a8148c34886d7ce30615d3799219b278fda29fac5f0d474ada1c087806e77a2edf1c0bd61b5438b94ced658e494d0f01c210373254ffc6166068d7d0b781363f721f59a2ae102e352a14a6de83e7942858b6f57ae';

