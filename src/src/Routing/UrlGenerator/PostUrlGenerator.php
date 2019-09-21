<?php

namespace App\Routing\UrlGenerator;

use App\Entity\Post;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as SymfonyUrlGenerator;
use Symfony\Component\Routing\Route;

class PostUrlGenerator extends SymfonyUrlGenerator implements VersatileGeneratorInterface
{
    /**
     * @var string
     */
    public const ROUTE_DETAIL = 'front_post_detail';

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return static::ROUTE_DETAIL === $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDebugMessage($name, array $parameters = [])
    {
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = SymfonyUrlGenerator::ABSOLUTE_PATH)
    {
        if (static::ROUTE_DETAIL !== $name) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        $key = 'post';

        if (!isset($parameters[$key])) {
            throw new MissingMandatoryParametersException(sprintf('Parameter "%s" is mandatory.', $key));
        }

        $post = $parameters[$key];

        if (!$post instanceof Post) {
            throw new MissingMandatoryParametersException(sprintf('Parameter "%s" must be an instance of "%s".', $key, Post::class));
        }

        unset($parameters[$key]);

        $route = new Route($post->getUrl());
        $compiledRoute = $route->compile();

        return $this->doGenerate(
            $compiledRoute->getVariables(),
            $route->getDefaults(),
            $route->getRequirements(),
            $compiledRoute->getTokens(),
            $parameters,
            $name,
            $referenceType,
            $compiledRoute->getHostTokens(),
            $route->getSchemes()
        );
    }
}
