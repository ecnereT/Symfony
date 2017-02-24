<?php

namespace IKNSA\BlogBundle\DataFixtures\ORM;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use IKNSA\BlogBundle\Entity\Post;

class LoadPost extends AbstractFixture implements \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setTitle('Montitre');
        //$post->setSummary('Il était une fois');
        $post->setContent("Le contenu");
        $post->setImage("Ah !");
        $post->setUser($this->getReference('user'));
        $manager->persist($post);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 200;
    }
}