<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blog = new Blog();
        $blog->setTitle('The first article');
        $blog->setText('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sollicitudin fermentum enim. Sed lectus elit, vestibulum quis odio at, posuere condimentum nulla. Proin placerat urna erat, et sodales diam blandit eu. Aenean facilisis arcu ac placerat placerat. Donec vitae egestas ipsum, ut interdum enim.</p>');
        $blog->setDate(new DateTime('2019-09-20'));
        $blog->setUrl('/first-article');
        $blog->setTags(['first', 'beginning']);
        $blog->setEnabled(true);
        $manager->persist($blog);

        $blog = new Blog();
        $blog->setTitle('The second article');
        $blog->setText('<p>Maecenas ac posuere arcu. Ut vitae turpis nec orci convallis ultricies. Praesent eget vestibulum odio. Ut et purus consequat, condimentum odio non, semper magna. Sed dignissim ligula eget sapien molestie, in congue dolor elementum. Praesent erat enim, ullamcorper a augue in, faucibus molestie nunc. Sed ut maximus elit, nec convallis enim. Aliquam cursus euismod purus at lacinia. Etiam lacinia fringilla lorem et tincidunt.</p>');
        $blog->setDate(new DateTime('2019-09-19'));
        $blog->setUrl('/second-article');
        $blog->setTags(['second']);
        $blog->setEnabled(true);
        $manager->persist($blog);

        $blog = new Blog();
        $blog->setTitle('The third article');
        $blog->setText('<p>Suspendisse cursus erat nec ante tristique, ut malesuada sapien malesuada. Sed faucibus sagittis orci sagittis sagittis. Nam ac lacus turpis. Duis sed lectus sed orci laoreet tincidunt. Aliquam erat volutpat. Vivamus a tempor nisl. Suspendisse potenti. Vivamus auctor dignissim tortor, eu laoreet dui facilisis et. Etiam finibus in velit eget ornare.</p>');
        $blog->setDate(new DateTime('2019-09-21'));
        $blog->setUrl('/third-article');
        $manager->persist($blog);

        $blog = new Blog();
        $blog->setTitle('The fourth article');
        $blog->setText('<p>Donec volutpat mauris ut tellus molestie, sed maximus elit lacinia. Aenean commodo a ipsum a aliquet. Curabitur auctor urna ac orci aliquet tempus. Donec eu dui efficitur, accumsan lacus vitae, vestibulum mi. Curabitur non elit ultricies libero ultricies interdum. Curabitur eleifend quis diam commodo mattis.</p>');
        $blog->setDate(new DateTime('2019-09-18'));
        $blog->setUrl('/fourth-article');
        $blog->setTags(['fourth', 'end']);
        $blog->setEnabled(true);
        $manager->persist($blog);

        $manager->flush();
    }
}
