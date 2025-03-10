<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use StefanFroemken\Mysqlreport\Domain\Model\StatusValues;

/**
 * Test case.
 */
class StatusValuesTest extends UnitTestCase
{
    /**
     * @var StatusValues
     */
    private $subject;

    protected function tearDown(): void
    {
        unset(
            $this->subject
        );
    }

    /**
     * @test
     */
    public function givenArrayIsAccessibleOverProperties(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);

        self::assertSame(
            'Stefan',
            $this->subject['firstName']
        );
        self::assertSame(
            'Froemken',
            $this->subject['lastName']
        );
    }

    /**
     * @test
     */
    public function arrayIsAccessibleWithIsset(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);

        self::assertTrue(
            isset($this->subject['firstName'])
        );
        self::assertFalse(
            isset($this->subject['middleName'])
        );
    }

    /**
     * @test
     */
    public function propertyIsWriteable(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);
        $this->subject['title'] = 'Bughunter';

        self::assertSame(
            'Bughunter',
            $this->subject['title']
        );
    }

    /**
     * @test
     */
    public function propertyIsRemovable(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);
        unset($this->subject['lastName']);

        self::assertSame(
            'Stefan',
            $this->subject['firstName']
        );
        self::assertFalse(
            isset($this->subject['lastName'])
        );
    }

    /**
     * @test
     */
    public function objectIsNotCountable(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);

        self::assertNotInstanceOf(
            \Countable::class,
            $this->subject
        );
    }

    /**
     * @test
     */
    public function objectIsNotTraversable(): void
    {
        $data = [
            'firstName' => 'Stefan',
            'lastName' => 'Froemken',
        ];

        $this->subject = new StatusValues($data);

        self::assertNotInstanceOf(
            \Traversable::class,
            $this->subject
        );
    }
}
