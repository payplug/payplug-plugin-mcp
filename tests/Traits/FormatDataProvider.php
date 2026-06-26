<?php

declare(strict_types=1);

namespace PayPlugPluginMcp\Tests\Traits;

trait FormatDataProvider
{
    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidArrayFormatDataProvider(): \Generator
    {
        yield [42];

        yield [null];

        yield [false];

        yield ['lorem ipsum'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidBoolFormatDataProvider(): \Generator
    {
        yield ['lorem Ipsum'];

        yield [42];

        yield [['key' => 'value']];

        yield [null];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidIntegerFormatDataProvider(): \Generator
    {
        yield [null];

        yield [['key' => 'value']];

        yield [true];

        yield ['lorem ipsum'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidFloatFormatDataProvider(): \Generator
    {
        yield [null];

        yield [['key' => 'value']];

        yield [true];

        yield ['lorem ipsum'];

        yield [42];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidNumericFormatDataProvider(): \Generator
    {
        yield [null];

        yield [['key' => 'value']];

        yield [true];

        yield ['lorem ipsum'];

        yield ['123abc'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidJSONFormatDataProvider(): \Generator
    {
        yield [''];

        yield ['{"feature": \'value\'}'];

        yield ['{"feature": "value", }'];

        yield ['{{}}'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidObjectFormatDataProvider(): \Generator
    {
        yield [42];

        yield [['key' => 'value']];

        yield [true];

        yield ['lorem ipsum'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidStringFormatDataProvider(): \Generator
    {
        yield [42];

        yield [['key' => 'value']];

        yield [false];

        yield [null];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidEmailFormatDataProvider(): \Generator
    {
        yield ['@test.com'];

        yield ['email@test'];

        yield ['emailtest.com'];
    }

    /** @return \Generator<int, array<mixed>, mixed, void> */
    public function invalidPhoneFormatDataProvider(): \Generator
    {
        yield [42];

        yield ['invalid phone number'];

        yield [false];

        yield [null];
    }
}
