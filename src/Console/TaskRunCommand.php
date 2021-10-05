<?php

declare(strict_types=1);

namespace TTBooking\TaskScheduling\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use TTBooking\TaskScheduling\Contracts\Task;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TaskRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:run {task : Task FQCN}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute scheduled task immediately';

    /**
     * Execute the console command.
     *
     * @param  Container  $container
     * @param  Dispatcher  $dispatcher
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function handle(Container $container, Dispatcher $dispatcher): void
    {
        /** @psalm-suppress MixedArgumentTypeCoercion, PossiblyNullArgument */
        $task = str_replace('/', '\\', $this->argument('task'));

        if (! class_exists($task)) {
            throw new InvalidArgumentException("Task [$task] not found.");
        }

        if (! is_subclass_of($task, Task::class)) {
            throw new InvalidArgumentException("Class [$task] must implement [".Task::class."] interface.");
        }

        $dispatcher->dispatchSync($container->make($task));

        $this->info("Task <comment>[$task]</comment> successfully finished!");
    }
}
