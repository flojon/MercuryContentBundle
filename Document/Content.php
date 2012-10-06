<?php

namespace Koala\Bundle\MercuryContentBundle\Document;

use Symfony\Cmf\Component\Routing\RouteAwareInterface;

class Content implements RouteAwareInterface
{
    protected $path;
    protected $node;
    protected $name;
    protected $parent;
    protected $regions = array();
    protected $title;
    protected $routes;

    public function getPath()
    {
        return $this->path;

    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function setNode($node)
    {
        $this->node = $node;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getRegion($name)
    {
        return $this->regions[$name]; // TODO should we return empty string if not existing?
    }

    public function setRegion($name, $content)
    {
        $this->regions[$name] = $content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getRoutes()
    {
        return $this->routes->toArray();
    }
}
