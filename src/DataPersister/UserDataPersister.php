<?php


namespace App\DataPersister;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements \ApiPlatform\Core\DataPersister\DataPersisterInterface
{
    private $entityManager;

    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
