<?php

use StartTestingPHP\Models\Task;
use StartTestingPHP\Repositories\TaskRepository;

class TaskRepositoryTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var TaskRepository
     */
    private $subject;

    /**
     * @var \Mockery\Mock
     */
    private $dbConnection;

    public function setUp()
    {
        $this->dbConnection = Mockery::mock(mysqli::class);

        $this->subject = new TaskRepository($this->dbConnection);
    }

    public function testAllWhenFalseReturnsEmpty()
    {
        $this->dbConnection->shouldReceive('query')
            ->with('SELECT note FROM tasks ORDER BY created DESC')
            ->andReturn(false);

        $this->assertSame([], $this->subject->all());
    }

    public function testAllWhenNoResultsReturnsEmpty()
    {
        $result = new stdClass();
        $result->num_rows = 0;

        $this->dbConnection->shouldReceive('query')
            ->with('SELECT note FROM tasks ORDER BY created DESC')
            ->andReturn($result);

        $this->assertSame([], $this->subject->all());
    }

    public function testAllReturnsTasks()
    {
        $result = Mockery::mock('mysqli_result_mock')->shouldIgnoreMissing();

        $result->num_rows = 2;
        $result->shouldReceive('fetch_assoc')
            ->andReturn(
                ['id' => 1, 'note' => 'task 1'],
                ['id' => 2, 'note' => 'task 2'],
                false
            );

        $this->dbConnection->shouldReceive('query')
            ->with('SELECT note FROM tasks ORDER BY created DESC')
            ->andReturn($result);

        $actual = $this->subject->all();

        $this->assertCount(2, $actual);
        $this->assertEquals('task 1', $actual[0]->getNote());
        $this->assertEquals('task 2', $actual[1]->getNote());

        $result->shouldHaveReceived('free');
    }

    public function testCreateReturnsNote()
    {
        $statement = Mockery::spy('mysql_stmt_mock');
        $statement->shouldReceive('bind_param')
            ->with('s', 'start testing');

        $statement->shouldReceive('execute')
            ->andReturn(true);

        $this->dbConnection->shouldReceive('prepare')
            ->with('INSERT INTO tasks (note, created) VALUES (?, NOW())')
            ->andReturn($statement);

        $actual = $this->subject->create('start testing');

        $this->assertInstanceOf(Task::class, $actual);
        $this->assertEquals('start testing', $actual->getNote());

        $statement->shouldHaveReceived('close');
    }

    public function testCreateThrowsException()
    {
        $this->dbConnection->shouldReceive('prepare')
            ->with('INSERT INTO tasks (note, created) VALUES (?, NOW())')
            ->andReturn(false);
        $this->dbConnection->shouldReceive('getError')
            ->andReturn('oh noes');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('oh noes');

        $this->subject->create('start testing');
    }

    public function testCreateReturnsFalse()
    {
        $statement = Mockery::mock('mysql_stmt_mock')->shouldIgnoreMissing();
        $statement->shouldReceive('bind_param')
            ->with('s', 'start testing');

        $statement->shouldReceive('execute')
            ->andReturn(false);

        $this->dbConnection->shouldReceive('prepare')
            ->with('INSERT INTO tasks (note, created) VALUES (?, NOW())')
            ->andReturn($statement);

        $actual = $this->subject->create('start testing');

        $this->assertFalse($actual);
    }
}