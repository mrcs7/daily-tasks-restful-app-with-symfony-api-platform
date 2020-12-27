<?php

namespace App\Factory;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Task|Proxy findOrCreate(array $attributes)
 * @method static Task|Proxy random()
 * @method static Task[]|Proxy[] randomSet(int $number)
 * @method static Task[]|Proxy[] randomRange(int $min, int $max)
 * @method static TaskRepository|RepositoryProxy repository()
 * @method Task|Proxy create($attributes = [])
 * @method Task[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class TaskFactory extends ModelFactory
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository=$userRepository;
    }

    protected function getDefaults(): array
    {
        $users= $this->userRepository->findAll();
        if(count($users)<1){
            UserFactory::new()->create();
            $users= $this->userRepository->findAll();
        }
        return [
            "creator" => $users[array_rand($users)],
            "date"=>self::faker()->dateTimeThisMonth('tomorrow'),
            "title" => self::faker()->realText(50),
            "description" => self::faker()->realText(500),
            "timeEstimate" => rand(1,10),
            "timeActual" => 0,
            "status" => rand(0,2)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Task $task) {})
        ;
    }

    protected static function getClass(): string
    {
        return Task::class;
    }
}
