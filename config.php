<?php

// RPC credentials, set in your ~/.coin/coin.conf file
$config['rpcuser'] = 'pgnuser';
$config['rpcpassword'] = 'pgnpw';
$config['rpcbind'] = '127.0.0.1';
$config['rpcport'] = '8756';

// The public receive address of your multisig wallet
$config['multisig'] = '';

// The scriptPubKey for that address. 
// Find this with coin-cli validateaddress MULTISIG_RCV_ADDRESS
$config['scriptPubKey'] = '';

// The redeemScript for this multisig wallet. You got it when you created the
// address. If you don't have it, well, there are ways to find it but that's
// beyond the scope of this utility.
$config['redeemScript'] = '';

// When consolidating, how many transactions to consolidate at once?
$config['utxo_limit'] = 50;

// How much do you want to pay for each transaction you broadcast?
$config['tx_fee'] = '0.01';

// Only consolidate amounts up to this amount:
$config['consolidate_amount'] = 250;

// Confirmations for maturity
$config['confirmations'] = 100;

