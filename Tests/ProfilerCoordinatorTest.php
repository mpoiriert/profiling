<?php

namespace Draw\Component\Profiling\Tests;

use Draw\Component\Profiling\ProfilerCoordinator;
use Draw\Component\Profiling\ProfilerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProfilerCoordinatorTest extends TestCase
{
    private const PROFILER_TYPE = 'test';

    private ProfilerCoordinator $object;

    /**
     * @var ProfilerInterface&MockObject
     */
    private ProfilerInterface $profiler;

    protected function setUp(): void
    {
        $this->object = new ProfilerCoordinator();
        $this->profiler = $this->createMock(ProfilerInterface::class);
    }

    public function testIsStartedDefault(): void
    {
        static::assertFalse($this->object->isStarted());
    }

    public function testIsStartedAfterStart(): void
    {
        $this->object->startAll();
        static::assertTrue($this->object->isStarted());
    }

    public function testIsStartedAfterStop(): void
    {
        $this->object->startAll();
        $this->object->stopAll();
        static::assertFalse($this->object->isStarted());
    }

    public function testRegisterProfile(): void
    {
        $this->profiler->expects(static::once())->method('getType')->willReturn(self::PROFILER_TYPE);
        $this->object->registerProfiler($this->profiler);
    }

    public function testStarAll(): void
    {
        $this->testRegisterProfile();
        $this->profiler->expects(static::once())->method('start');
        $this->object->startAll();
    }

    public function testStopAll(): void
    {
        $this->testStarAll();
        $this->profiler->expects(static::once())->method('stop')->willReturn($result = 'result');
        $metrics = $this->object->stopAll();

        static::assertTrue(isset($metrics->{self::PROFILER_TYPE}));
        static::assertSame($result, $metrics->{self::PROFILER_TYPE});
    }
}
