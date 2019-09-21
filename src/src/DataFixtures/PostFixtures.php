<?php

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setTitle('The first article');
        $post->setText('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sollicitudin fermentum enim. Sed lectus elit, vestibulum quis odio at, posuere condimentum nulla. Proin placerat urna erat, et sodales diam blandit eu. Aenean facilisis arcu ac placerat placerat. Donec vitae egestas ipsum, ut interdum enim.</p>');
        $post->setDate(new DateTime('2019-09-20'));
        $post->setUrl('/first-article');
        $post->setTags(['first', 'beginning']);
        $post->setEnabled(true);
        $manager->persist($post);

        $post = new Post();
        $post->setTitle('The second article');
        $post->setText('<p>Maecenas ac posuere arcu. Ut vitae turpis nec orci convallis ultricies. Praesent eget vestibulum odio. Ut et purus consequat, condimentum odio non, semper magna. Sed dignissim ligula eget sapien molestie, in congue dolor elementum. Praesent erat enim, ullamcorper a augue in, faucibus molestie nunc. Sed ut maximus elit, nec convallis enim. Aliquam cursus euismod purus at lacinia. Etiam lacinia fringilla lorem et tincidunt.</p>');
        $post->setDate(new DateTime('2019-09-19'));
        $post->setUrl('/second-article');
        $post->setTags(['second']);
        $post->setEnabled(true);
        $post->setViews(23);
        $manager->persist($post);

        $post = new Post();
        $post->setTitle('The third article');
        $post->setText('<p>Suspendisse cursus erat nec ante tristique, ut malesuada sapien malesuada. Sed faucibus sagittis orci sagittis sagittis. Nam ac lacus turpis. Duis sed lectus sed orci laoreet tincidunt. Aliquam erat volutpat. Vivamus a tempor nisl. Suspendisse potenti. Vivamus auctor dignissim tortor, eu laoreet dui facilisis et. Etiam finibus in velit eget ornare.</p>');
        $post->setDate(new DateTime('2019-09-21'));
        $post->setUrl('/third-article');
        $manager->persist($post);

        $post = new Post();
        $post->setTitle('The fourth article');
        $post->setText('<p>Donec volutpat mauris ut tellus molestie, sed maximus elit lacinia. Aenean commodo a ipsum a aliquet. Curabitur auctor urna ac orci aliquet tempus. Donec eu dui efficitur, accumsan lacus vitae, vestibulum mi. Curabitur non elit ultricies libero ultricies interdum. Curabitur eleifend quis diam commodo mattis.</p>');
        $post->setDate(new DateTime('2019-09-18'));
        $post->setUrl('/fourth-article');
        $post->setTags(['fourth', 'end']);
        $post->setEnabled(true);
        $post->setViews(67);
        $manager->persist($post);

        $manager->flush();
    }
}
