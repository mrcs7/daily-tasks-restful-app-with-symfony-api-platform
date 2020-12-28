<?php


namespace App\Doctrine\Listeners;


use App\Entity\Task;
use Symfony\Component\Security\Core\Security;

class TaskSetCreatorListener
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }
    public function prePersist(Task $task)
    {
        if($task->getCreator()){
            return;
        }
        if($this->security->getUser())
        {
            $task->setCreator($this->security->getUser());
        }

    }
}
