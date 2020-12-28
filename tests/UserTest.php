<?php


namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\AppFixtures;
use App\Entity\Task;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserTest extends ApiTestCase
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
    }

    /**
     * @test
     */
    public function it_should_create_user_without_authentication()
    {
        $this->init();
        $response = static::createClient()->request('POST', '/api/v1/users',[
            'json' => [
                "email" => "test87@examlpe.com",
                "password" => '$argon2id$v=19$m=65536,t=4,p=1$vPStj3ZFCCvvkksz9MF2+w$vMoZuibsn3N7Jh09RrIZqeo9UZJw6v31S5dEaXuxxEg',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        //Try To Login
        $tokenRequest = static::createClient()->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test87@examlpe.com',
                'password' => 'testtest'
            ]
        ]);
        $this->assertResponseIsSuccessful();

    }

    /**
     * @test
     */
    public function it_should_give_unauthorized_when_user_is_not_authenticated_except_for_create()
    {
        $this->init();

        $response = static::createClient()->request('GET', '/api/v1/users');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('PUT', '/api/v1/users/1');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('DELETE', '/api/v1/users/1');
        $this->assertResponseStatusCodeSame(401);
        $response = static::createClient()->request('GET', '/api/v1/users/1');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @test
     */
    public function it_should_list_users()
    {
        $this->init();
        $token = $this->userLogin();
        $response = static::createClient()->request('GET', '/api/v1/users',[
            'headers' => ['Authorization' => 'Bearer '.$token]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    /**
     * @test
     */
    public function it_should_update_user_info()
    {
        $this->init();
        $token = $this->userLogin();
        $response = static::createClient()->request('PUT', '/api/v1/users/6',[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "email" => "exampleTest@example.com",
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseIsSuccessful();

    }

    /**
     * @test
     */
    public function it_should_delete_user()
    {
        $this->init();
        $token = $this->userLogin();
        $response = static::createClient()->request('DELETE', '/api/v1/users/6',[
            'headers' => ['Authorization' => 'Bearer '.$token],
            'json' => [
                "email" => "exampleTest@example.com",
            ]
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @test
     */
    public function it_should_show_user_info()
    {
        $this->init();
        $token = $this->userLogin();
        $response = static::createClient()->request('GET', '/api/v1/users/6',[
            'headers' => ['Authorization' => 'Bearer '.$token],
        ]);
        $this->assertResponseStatusCodeSame(200);
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
