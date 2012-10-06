<?php

namespace Koala\Bundle\MercuryContentBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use PHPCR\Util\NodeHelper;

use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Bundle\RoutingExtraBundle\Document\Route;
use Symfony\Cmf\Bundle\RoutingExtraBundle\Document\RedirectRoute;
use Symfony\Cmf\Bundle\MultilangContentBundle\Document\MultilangLanguageSelectRoute;

class LoadRoutingData extends ContainerAware implements FixtureInterface, OrderedFixtureInterface
{
    public function getOrder()
    {
        return 21;
    }

    /**
     * Load routing data into the document manager.
     *
     * NOTE: We demo all possibilities. Of course, you should try to be
     * consistent in what you use and only use different things for special
     * cases.
     *
     * @param $dm
     */
    public function load(ObjectManager $dm)
    {
        $session = $dm->getPhpcrSession();

        $basepath    = $this->container->getParameter('symfony_cmf_routing_extra.routing_repositoryroot');
        $content_path = $this->container->getParameter('symfony_cmf_content.static_basepath');

        if ($session->itemExists($basepath)) {
            $session->removeItem($basepath);
        }

        NodeHelper::createPath($session, dirname($basepath));
        $root = $dm->find(null, dirname($basepath));

        $parent = new Route;
        $parent->setPosition($root, basename($basepath));
        $parent->setRouteContent($dm->find(null, "$content_path/home"));
        $dm->persist($parent);

        $dm->flush();
    }
}
