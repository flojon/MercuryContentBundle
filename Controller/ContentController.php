<?php

namespace Koala\Bundle\MercuryContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Bundle\ContentBundle\Controller\ContentController as BaseContentController;

class ContentController extends BaseContentController
{
    public function indexAction(Request $request, $contentDocument, $contentTemplate = null)
    {
        return parent::indexAction($request, $contentDocument, $contentTemplate);
    }
}
