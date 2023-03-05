<?php

namespace App\Command;

use App\Repository\Core\Tasks\TasksRepository;
use App\Repository\Core\Tasks\TasksStatusRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class TasksCommand extends Command
{
    private TasksRepository $tasks;
    private TasksStatusRepository $tasksStatus;
    private KernelInterface $kernel;

    /**
     * UpdateProductCommand constructor.
     */
    public function __construct(TasksStatusRepository $tasksStatusRepository, TasksRepository $tasksRepository, KernelInterface $kernel)
    {
        parent::__construct();

        $this->tasks = $tasksRepository;
        $this->tasksStatus = $tasksStatusRepository;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('tasks')
            ->setDescription('Komenda do uruchamiana zadań');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = $this->tasks->findBy(['status' => 1]);
        foreach ($tasks as $task) {
            $task->setStatus($this->tasksStatus->findOneBy(['value' => 'inprogress']));
            $this->tasks->save($task, true);

            $application = new Application($this->kernel);
            $application->setAutoExit(false);
            $input = new ArrayInput([
                'command' => $task->getCommend(),
                'param' => $task->getFile()->getFullPath(),
            ]);
            $output = new BufferedOutput();
            $application->run($input, $output);
            var_dump($output->fetch());

            $task->setStatus($this->tasksStatus->findOneBy(['value' => 'end']));
            $this->tasks->save($task, true);
        }

        return Command::SUCCESS;
    }
}
