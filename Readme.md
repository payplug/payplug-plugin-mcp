# Payplug Plugin Core

## Description
Module core for Payplug integration. Enables management of payments, merchants, and notifications via the Payplug API.

## Requirements
- PHP 7.2 or higher
- Composer
- Payplug PHP SDK (vendor payplug/payplug-php)

## Installation

#### Option 1 - Strongly recommanded via composer:

- Get composer from the `composer website`_.
- Make sure you have initialized your *composer.json*.
- Run *composer require payplug/payplug-php* from your project directory.

> _composer website: https://getcomposer.org/download/

#### Option 2 - clone the repository:
    
> git clone https://github.com/payplug/payplug-plugin-core.git

## Features
- Major features:
    - Merchant management (authentication, module configuration based on permissions)
    - Payment management (creation, cancellation, refund) on the Payplug API via the vendor payplug/payplug-php
    - Webhook for receiving Payplug notifications
    - Saved payer card management in the Payplug API

- Minor features:
    - Error and log management
    - Validation of received data
    - Module configuration (API key, merchant parameters)
    - Multilingual support (messages, notifications)
    - HTTP response management (statuses, messages)
    - Basic user interface management (confirmation/error messages)

## Structure of the module
    src  
    ├── actions: All actions related to a workflow  
    ├── utilities  
    │   ├── helpers: Reusable static methods, generic, non-business  
    │   ├── validators: Data validation (validator)  
    │   └── traits: Reusable methods for one or more classes/interfaces  
    ├── services: Ready-to-use object (single task)  
    ├── models  
    │   ├── repositories: Communication with the database
    │   ├── entities: Definition of an object and its attributes (getter/setter)  
    │   └── classes  
    │       ├── Abstract classes: Base models not instantiable (gateway, address, card)  
    │       ├── Extended classes  
    │       └── Final classes: Final classes not extendable (ipn, lock)  
    └── interfaces: Definition of methods specific to a class

## Endpoint métiers du module Payplug
- Merchant experience and actions:
    - user login
    - user logout
    - get user permissions
    - validate module requirements
    - configure payment features
    - abort payment resource
    - capture payment resource
    - refund payment resource

- User experience and actions:
    - initiate payment
    - delete saved card

- Automated actions:
    - on order creation
    - on language addition/modification/deletion
    - on order state addition/modification/deletion
    - on order history addition

- Display hooks:
    - display customer account
    - display CTA button
    - display order statuses form
    - display validation
    - display header
    - display payment options
    - display admin order content
    - display admin plugin configuration

- Webhook (notifications):
    - handle payment notification use to create/update order
    - handle refund notification
  
