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
    protected $options;
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

    public function getRegions()
    {
        return $this->regions;
    }

    public function setRegions($regions) // Mercury data
    {
        foreach ($regions as $name=>$content) {
            $value = ($content["type"] == "image") ? $content['attributes']['src'] : $content['value'];
            $this->setRegion($name, $value);
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getRoutes()
    {
        return $this->routes->toArray();
    }
}
