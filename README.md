MultiSigAddressManager
============

Documentation
=============

* [Overview](#overview)
* [Requirements and Installation](#installation)
* [Usage](#usage)
* [Credits](#credits)

Overview
--------

In blockchain-based cryptocurrencies, UTXOs can pile up and make it different to spend funds in a single transaction. When this happens, UTXOs need to be consolidated into higher value UTXOs so they can be spent. It is not possible to send a transaction with thousands of unspents. The problem is exacerbated with multisig wallets because each signing party must sign each and every transaction in the consolidation process.

The MultiSigAddressManager was written by Traysi to provide a tool for multisig wallet signers to more conveniently consolidate their funds and to spend those funds.

This tool generates transactions and helps you sign them in a convenient way. It is provided with no warranties of perfection, and may have bugs. At every step of the way you will be given raw transactions that you can easily verify using the decoderawtransaction RPC call. It is your responsibility to be sure that each transaction is correct and is doing what you want it to do. You accept full responsibility for any transaction that you broadcast.

Requirements and Installation
------------

This utility was intended to be used on Linux. The author uses Ubuntu 16.04 but any Linux or Unix variant should work fine. It requires PHP. It probably works fine on Windows but this has not been tested. Each signer of your multisig wallet should install this utility and customize it for their own signing situation.

[PHP](http://php.net) (Ubuntu 16):

    $ sudo apt-get -y install php7.0-cli php-curl

Or if you're on Ubuntu 18:

[PHP](http://php.net) (Ubuntu 18):

    $ sudo apt-get -y install php7.2-cli php-curl

Clone the MultiSigAddressManager repository:

    $ git clone https://github.com/traysi/MultiSigAddressManager
    $ cd MultiSigAddressManager

This utility requires that you have the coin daemon running for this coin, accepting RPC communication. Edit your .conf file for the daemon and set the rpc port, username, and password. Once the coin daemon is running, you need to add the multisig public address as a watchonly address into this wallet. This step is only needed on the computer that generates the initial transaction set. The other signers don't need this part.

    $ coin-cli importaddress MULTISIG_RCV_ADDR "" true

That step may take a little while to complete. Once the watchonly address is added, your daemon will be able to see all transactions involving this address. You can confirm that with:

    $ coin-cli listunspent 0 99999999 "[\"MULTISIG_RCV_ADDR\"]"

That should print a pile of UTXOs. If you see a bunch of stuff, you're ready to proceed with configuring the utility. In the MultiSigAddressManager directory, edit the config.php file and set the important variables. You need to have the multisig public address, redeemScript, scriptPubKey, as well as your own private key for your signing. The other configuration options should all be self-explanatory.

Usage
-----

To consolidate the wallet, run the first script

    $ ./1_create_consolidation_txes.php 

If successful, it will produce a series of raw transactions, one per line. If you see that, then that means it's working and you can then store the result.

    $ ./1_create_consolidation_txes.php > txes

Now you need to take the txes file and have each signer sign those transactions, one at a time. So send the txes file to Alice and have her run it like:

    $ ./2_sign_transactions.php txes alice_private_key 

If she sees a bunch of output lines, then she's successfully signing it. So run it again and send the output to a **new** file.

    $ ./2_sign_transactions.php txes her_private_key > txes.alice

Now Alice needs to send txes.alice over to Bob and he will repeat the process:

    $ ./2_sign_transactions.php txes.alice bob_private_key 

If he sees a bunch of output lines, then he's successfully signing it. So run it again and send the output to a **new** file.

    $ ./2_sign_transactions.php txes.alice bob_private_key > txes.bob

Once everyone has signed, you will have a final file of transactions that you can then broadcast. Do that with:

    $ ./3_broadcast_transactions.php txes.signed

If successful, that will print out a series of transaction IDs for each transaction that has been broadcast to the network. Your wallet is now consolidated and you can later spend large funds from it.

Now that it's consolidated, to spend large amounts of funds from the wallet, first let your consolidated funds confirm on the network. 100 confirmations is reasonable for most fast blockchains. Then generate the spending transaction like so:

    $ ./spend_funds.php RECIPIENT_ADDRESS AMOUNT > tx

tx is now a file containing the single transaction that sends AMOUNT to RECIPIENT_ADDRESS. At this point, I highly recommend you verify the transaction like this:

    $ coin-cli decoderawtransaction `cat tx`

That will print out the details of the transaction. Make sure it looks right before proceeding! If you like the transaction, then have Alice, Bob, and Dave, et al sign it using the 2_sign_transactions.php exactly as you did the consolidation steps, then when you have enough signatures, broadcast it using 3_broadcast_transactions.php.

Credits
-----

This tool was made by Traysi of [MinerMore.com](https://minermore.com/) for a bounty from the Pigeoncoin team.
