# CryptoGate

CryptoGate allows you to create invoices for BTC/BCH/LTC/Dash coins.
As soon as an invoice is paid, the user gets returned to the purchasing site.

![Example Invoice](readme.png)

To test cryptogate run setup.sh to generate the .env config file.

    ./setup.sh
    
Build our docker container by running

    docker-compose build

Apply your customizations to the .env file and start the CryptoGate container by:

    docker-compose up -d

For more information please visit our german website [www.cryptopanel.de](https://www.cryptopanel.de).

## License
Code released under [the MIT license](https://github.com/lamp-aw/docker-ltc-litecore/blob/master/LICENSE).