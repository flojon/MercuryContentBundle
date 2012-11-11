<?php

namespace Koala\Bundle\MercuryContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\ContentBundle\Controller\ContentController as BaseContentController;
use Doctrine\Common\Persistence\ObjectManager;

class ContentController extends BaseContentController
{
    protected $om;
    protected $save_method;

    public function __construct(ObjectManager $om, EngineInterface $templating, $defaultTemplate, $save_method)
    {
        parent::__construct($templating, $defaultTemplate);
        $this->om = $om;
        $this->save_method = $save_method;
    }

    public function indexAction(Request $request, $contentDocument, $contentTemplate = null)
    {
        if (in_array($request->getMethod(), array('PUT', 'POST'))) {
            $contentDocument->setRegions($request->request->get('content'));
            $this->om->flush();
            return new Response("", 200, array('Content-Type'=>'application/json'));
        }

        // TODO check permission
        $contentTemplate = $request->query->get('mercury_frame') ? $contentTemplate : "KoalaMercuryContentBundle:Content:mercury.html.twig";

        return parent::indexAction($request, $contentDocument, $contentTemplate);
    }

    protected function getParams(Request $request, $contentDocument)
    {
        return array(
            'page' => $contentDocument,
            'title' => $contentDocument->getTitle(),
            'save_method' => $this->save_method,
        );
    }

}
