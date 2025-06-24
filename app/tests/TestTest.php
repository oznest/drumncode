<?php

namespace App\Tests;

use App\Domain\Entity\Task;
use App\Domain\Entity\User;
use App\Domain\Enum\TaskStatus;
use Test;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testNestedLevel2()
    {
        $user = $this->createMock(User::class);
        $task = new Task($user);
        $task->setStatus(TaskStatus::TODO);

        $task1 = new Task($user);
        $task1->setStatus(TaskStatus::TODO);
        $task1->setParent($task);

        $this->assertTrue($task->hasActiveSubtasks());
    }

    public function testNestedLevel3()
    {
        $user = $this->createMock(User::class);
        $task = new Task($user);
        $task->setStatus(TaskStatus::DONE);

        $task1 = new Task($user);
        $task1->setStatus(TaskStatus::DONE);
        $task1->setParent($task);

        $task2 = new Task($user);
        $task2->setStatus(TaskStatus::TODO);
        $task2->setParent($task1);
        $this->assertTrue($task->hasActiveSubtasks());
    }

    public function testNoActiveSubtasks()
    {
        $user = $this->createMock(User::class);
        $task = new Task($user);
        $task->setStatus(TaskStatus::TODO);

        $task1 = new Task($user);
        $task1->setStatus(TaskStatus::DONE);
        $task1->setParent($task);

        $this->assertFalse($task->hasActiveSubtasks());
    }

    public function testIsDone()
    {
        $user = $this->createMock(User::class);
        $task = new Task($user);
        $task->setStatus(TaskStatus::DONE);
        $this->assertTrue($task->isDone());
    }

    public function testIsDoneNested()
    {
        $user = $this->createMock(User::class);

        $task1 = new Task($user);
        $task2 = new Task($user);
        $task3 = new Task($user);

        $task2->setParent($task1);
        $task3->setParent($task2);

        $task3->setStatus(TaskStatus::DONE);
        $this->assertTrue($task3->isDone());
        $this->assertFalse($task2->isDone());
        $this->assertFalse($task1->isDone());


        $task2->setStatus(TaskStatus::DONE);
        $this->assertTrue($task3->isDone());
        $this->assertTrue($task2->isDone());
        $this->assertFalse($task1->isDone());

        $task1->setStatus(TaskStatus::DONE);
        $this->assertTrue($task3->isDone());
        $this->assertTrue($task2->isDone());
        $this->assertTrue($task1->isDone());
    }

    public function testRiseException()
    {
        $user = $this->createMock(User::class);
        $task1 = new Task($user);
        $task2 = new Task($user);
        $task2->setParent($task1);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This task has active subtasks.');
        $task1->setStatus(TaskStatus::DONE);
    }
}
