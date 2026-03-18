<?php

declare(strict_types=1);

namespace PayPlug\tests\mock;

class PaymentInputDTOMock
{
    public static function get()
    {
        $paymentInputDTO = new \stdClass();

        $paymentInputDTO->api_bearer = 'token_bearer';
        $paymentInputDTO->payment_method = 'payment method';
        $paymentInputDTO->amount = 4242;
        $paymentInputDTO->currency_iso_code = 'EUR';
        $paymentInputDTO->customer = [
            'identifier' => 42,
            'billing' => [
                'title' => 'mr',
                'first_name' => 'John',
                'last_name' => 'Watson',
                'mobile_phone_number' => '+33612345678',
                'email' => 'john.watson@example.net',
                'address1' => '221B Baker Street',
                'postcode' => 'NW16XE',
                'city' => 'London',
                'country' => 'GB',
                'language' => 'en',
            ],
            'shipping' => [
                'title' => 'mr',
                'first_name' => 'John',
                'last_name' => 'Watson',
                'mobile_phone_number' => '+33612345678',
                'email' => 'john.watson@example.net',
                'address1' => '221B Baker Street',
                'postcode' => 'NW16XE',
                'city' => 'London',
                'country' => 'GB',
                'language' => 'en',
            ],
        ];
        $paymentInputDTO->urls = [
            'return' => 'https://example.net/success?id=42',
            'cancel' => 'https://example.net/cancel?id=42',
            'notification' => 'https://example.net/notifications?id=42',
        ];
        $paymentInputDTO->metadata = [
            'cart ID' => '42',
            'custom data' => 'lorem ipsum',
        ];
        $paymentInputDTO->context = [];

        return $paymentInputDTO;
    }
}
