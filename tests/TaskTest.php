<?php


namespace App\Tests;



use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\AppFixtures;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class TaskTest extends ApiTestCase
{
    use FixturesTrait;

    public function init()
    {
        self::bootKernel();
        $this->loadFixtures(array(
            AppFixtures::class,
        ));
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$vPStj3ZFCCvvkksz9MF2+w$vMoZuibsn3N7Jh09RrIZqeo9UZJw6v31S5dEaXuxxEg');
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $task= new Task();
        $task->setCreator($user);
        $task->setDate(new \DateTime());
        $task->setTitle('NewTask');
        $task->setStatus(0);
        $task->setTimeEstimate(15);
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($task);
        $em->flush();
    }

    /**
     * @test
     */
    public function it_should_list_tasks_when_user_authenticated()
    {
        $this->init();
        $token = $this->userLogin();
        $response = static::createClient()->request('GET', '/api/v1/tasks',[
            'headers' => ['Authorization' => 'Bearer '.$token]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertCount(5, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Task::class);
    }

    /**
     * @test
     */
    public function it_should_give_unauthorized_when_user_is_not_authenticated()
    {
        $this->init();

        $response = static::createClient()->request('GET', '/api/v1/tasks');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('POST', '/api/v1/tasks');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('PUT', '/api/v1/tasks/1');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('DELETE', '/api/v1/tasks/1');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('GET', '/api/v1/tasks/1');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @test
     */
    public function it_should_create_task()
    {
        $this->init();

        $token = $this->userLogin();
        $response = static::createClient()->request('POST', '/api/v1/tasks',[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @test
     */
    public function it_should_delete_a_user_task()
    {
        $this->init();

        $token = $this->userLogin();
        $createdTask = static::createClient()->request('POST', '/api/v1/tasks',[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $task_id= json_decode($createdTask->getContent(false))->id;
        $response = static::createClient()->request('DELETE', '/api/v1/tasks/'.$task_id,[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @test
     */
    public function it_should_update_a_user_task()
    {
        $this->init();

        $token = $this->userLogin();
        $createdTask = static::createClient()->request('POST', '/api/v1/tasks',[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $task_id= json_decode($createdTask->getContent(false))->id;
        $response = static::createClient()->request('PUT', '/api/v1/tasks/'.$task_id,[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task2",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function it_should_give_unauthorized_when_user_try_to_update_task_created_by_others()
    {
        $this->init();
        $token = $this->userLogin();
        $task_id= 7;
        $response = static::createClient()->request('PUT', '/api/v1/tasks/'.$task_id,[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "title" => "Test Task2",
                "date" => "2020-12-28",
                "status" => 0
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @test
     */
    public function it_should_give_unauthorized_when_user_try_to_delete_task_created_by_others()
    {
        $this->init();
        $token = $this->userLogin();
        $task_id= 3;
        $response = static::createClient()->request('DELETE', '/api/v1/tasks/'.$task_id,[
            'headers' => ['Authorization' => 'Bearer '.$token],
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    private function userLogin()
    {
        $tokenRequest = static::createClient()->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => 'testtest'
            ]
        ]);
        return json_decode($tokenRequest->getContent(false))->token;
    }
}
