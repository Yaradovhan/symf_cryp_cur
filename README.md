
# Symfony Crypto Coin Data

This is the test job

## Main things

- you can get data about currency exchane rate
- you can get data about pair crypto coin / currency (ex. BTC/USD, BTC/EUR)
- you can use API to get collection with crypto coin / currency


# API

link to get collection data

`{domain}/api/crypto-price/pair-data/{symbol}/{currency}`

you can use variable for pagination

`{domain}/api/crypto-price/pair-data/{symbol}/{currency}?page=2&itemsPerPage=20`

# Command and scheduled tasks

`bin/console app:update-currency-rate`  this command run fetch currency rate from 3d party API, update and set data to DB

`bin/console crypto:update-prices` this command run fetch crypto prices pair (usdt as default) from Binance API, update and set data to DB

run command for start scheduller

`bin/console messenger:consume -v  scheduler_default`

bin/console crypto:update-prices scheduled run every 1 hour

bin/console app:update-currency-rate scheduled run every 1 day

