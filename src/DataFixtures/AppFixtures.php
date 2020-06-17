<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\PostLike;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = [];

        $user = new User();
        $user->setEmail('user@gmail.com')->setPassword($this->encoder->encodePassword($user, 'password'));
        $manager->persist($user);

        $users[] = $user;

        for ($i = 0; $i < 20; $i++){
            $user = new User();
            $user->setEmail($faker->email)->setPassword($this->encoder->encodePassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        for($i = 0; $i < 20; $i++){
            $post = new Post();
            $post->setTitle($faker->title)
                ->setImg('https://picsum.photos/id/' . $i .'/300/150')
                ->setContent(join(',', $faker->paragraphs()));
            $manager->persist($post);
            for ($j=0; $j < mt_rand(0, 15) ; $j++) { 
                $like = new PostLike();
                $like->setPost($post)->setUser($faker->randomElement($users));
                $manager->persist($like);
            }
        }
        $manager->flush();
    }
}
