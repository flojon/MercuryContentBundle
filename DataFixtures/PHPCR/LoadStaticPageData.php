<?php

namespace Koala\Bundle\MercuryContentBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use PHPCR\Util\NodeHelper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Parser;

class LoadStaticPageData extends ContainerAware implements FixtureInterface, OrderedFixtureInterface
{
    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        $session = $manager->getPhpcrSession();
        $basepath = $this->container->getParameter('symfony_cmf_content.static_basepath');        

        NodeHelper::createPath($session, $basepath);

        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents(__DIR__ . '/../static/page.yml'));

        foreach ($data['static'] as $overview) {
            $path = $basepath . '/' . $overview['name'];
            $page = $manager->find(null, $path);
            if (! $page) {
                $class = isset($overview['class']) ? $overview['class'] : 'Koala\\Bundle\\MercuryContentBundle\\Document\\Content';
                $page = new $class();
                $page->setPath($path);
                $manager->persist($page);
            }
//            $page->setLayout( empty($overview['layout']) ? 'default' : $overview['layout'] );

            if (is_array($overview['title'])) {
                foreach ($overview['title'] as $locale => $title) {
                    $page->setTitle($title);
//                    $page->body = $overview['content'][$locale];
                    $manager->bindTranslation($page, $locale);
                }
            } else {
                $page->setTitle($overview['title']);
//                $page->body = $overview['content'];
            }
            if (isset($overview['regions'])) {
                foreach ($overview['regions'] as $name => $content) {
                    $page->setRegion($name, $content);
                }
            }
        }

        $manager->flush(); //to get ref id populated
    }

    private function getIdentifier($manager, $document)
    {
        $class = $manager->getClassMetadata(get_class($document));
        return $class->getIdentifierValue($document);
    }
}
