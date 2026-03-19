# MyAdmin PayPal Payments Plugin

[![Build Status](https://github.com/detain/myadmin-paypal-payments/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-paypal-payments/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-paypal-payments/version)](https://packagist.org/packages/detain/myadmin-paypal-payments)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-paypal-payments/downloads)](https://packagist.org/packages/detain/myadmin-paypal-payments)
[![License](https://poser.pugx.org/detain/myadmin-paypal-payments/license)](https://packagist.org/packages/detain/myadmin-paypal-payments)

PayPal payment processing plugin for the MyAdmin control panel. Provides integration with PayPal's NVP/SOAP APIs for Express Checkout, Digital Goods, recurring billing, subscription management, IPN webhook handling, and administrative refund workflows.

## Features

- **Express Checkout** -- Standard and Digital Goods payment flows via PayPal NVP API
- **Recurring Payments** -- Create and manage recurring billing profiles
- **Subscription Management** -- PayPal subscription link generation and lifecycle handling
- **IPN Processing** -- Parse and validate Instant Payment Notification messages
- **Administrative Tools** -- Transaction history, refund processing, and payment review pages
- **Sandbox Support** -- Toggle between live and sandbox environments for testing

## Requirements

- PHP 8.2 or higher
- Extensions: curl, mbstring, soap
- Symfony EventDispatcher 5.x, 6.x, or 7.x

## Installation

Install with Composer:

```sh
composer require detain/myadmin-paypal-payments
```

## Running Tests

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the [LGPL-2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html) license.
