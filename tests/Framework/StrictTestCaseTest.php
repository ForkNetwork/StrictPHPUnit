<?php declare(strict_types = 1);

namespace ForkNetwork\StrictPHPUnit\Tests\Framework;

use ForkNetwork\StrictPHPUnit\StrictTestTrait;
use ForkNetwork\StrictPHPUnit\Tests\Framework\Fixtures\GenericClass;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class StrictTestCaseTest extends TestCase
{
    use StrictTestTrait;

    /**
     * @var GenericClass
     */
    private $genericClass;

    /**
     * Set up the SUT.
     */
    protected function setUp(): void
    {
        $this->genericClass = new GenericClass();
    }

    /**
     * Assert that expectations are handled as they are always done.
     */
    public function testItShouldAcceptNormalExpectations(): void
    {
        $testCaseMock = $this->getMockBuilder(TestCase::class)
            ->getMock();

        $testCaseMock
            ->expects($this->once())
            ->method('hasExpectationOnOutput')
            ->willReturn(true);

        $result = $this->genericClass->callsOneMethod($testCaseMock);

        $this->assertTrue($result);
    }

    /**
     * Assert that an unexpected method call throws an exception instead of silently continuing.
     */
    public function testItShouldFailOnUnexpectedMethodCalls(): void
    {
        $testCaseMock = $this->getMockBuilder(TestCase::class)
            ->getMock();

        $this->expectException(ExpectationFailedException::class);

        $this->genericClass->callsOneMethod($testCaseMock);
    }

    /**
     * Assert that the now default disabled auto return value generation can be enabled again.
     */
    public function testItShouldAcceptUnexpectedMethodCallsWhenExplicitlyConfigured(): void
    {
        $testCaseMock = $this->getMockBuilder(TestCase::class)
            ->enableAutoReturnValueGeneration()
            ->getMock();

        $result = $this->genericClass->callsOneMethod($testCaseMock);

        $this->assertFalse($result, 'The default generated return value is false.');
    }

    /**
     * Assert that the createMock method also disallows unexpected method calls.
     * (i.e. it is using the getMockBuilder method)
     */
    public function testItShouldFailForCreateMockMethod(): void
    {
        $testCaseMock = $this->createMock(TestCase::class);

        $this->expectException(ExpectationFailedException::class);

        $this->genericClass->callsOneMethod($testCaseMock);
    }
}
