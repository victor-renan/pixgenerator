# victor-renan/pixgenerator

A library with no-dependency for generating PIX copy-and-paste codes for static QR Codes. Based on [Pix Manual Reference](https://www.bcb.gov.br/content/estabilidadefinanceira/pix/Regulamento_Pix/II_ManualdePadroesparaIniciacaodoPix.pdf) By the BCB (Banco Central do Brasil).

## Installation

Latest version via [Packagist](https://packagist.org/packages/victor-renan/pixgenerator):

```bash

composer require victor-renan/pixgenerator

```

## Usage

### Getting Started

```php

<?php
// Import the PixGenerator class
use VictorRenan\PixGenerator\PixGenerator;

// Creates an instance using your Pix key
$generator = new PixGenerator('<PixKey>');

// Create the static code
$code = $generator->getCode()

```

### Customizing the generated code

```php

// ...

// Sets the transaction amount
$generator->setTransactionAmount(1.00);

// Sets the transaction id
$generator->setTransactionId('txId');

// Sets the merchant name
$generator->setMerchantName('Jonh Doe');

// Sets the merchant city
$generator->setMerchantCity('Brasilia');

// Sets additional info message
$generator->setAdditionalInfo('Message');

```

## Testing

Clone this repoitory and install the dev-dependencies via Composer. After this, you can run PHPUnit:

```bash

./vendor/bin/phpunit

``` 
