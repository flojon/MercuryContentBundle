<?php

namespace Koala\Bundle\MercuryContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Cmf\Bundle\ContentBundle\Controller\ContentController as BaseContentController;
use Doctrine\Common\Persistence\ObjectManager;

class ContentController extends BaseContentController
{
    protected $om;
    protected $requiredRole;
    protected $securityContext;
    protected $upload;
    protected $save_method;

    public function __construct(
        ObjectManager $om,
        EngineInterface $templating,
        $defaultTemplate,
        SecurityContextInterface $securityContext = null,
        $requiredRole = "IS_AUTHENTICATED_ANONYMOUSLY",
        $save_method,
        $upload
    ) {
        parent::__construct($templating, $defaultTemplate);
        $this->om = $om;
        $this->securityContext = $securityContext;
        $this->requiredRole = $requiredRole;
        $this->save_method = $save_method;
        $this->upload = $upload;
    }

    public function indexAction(Request $request, $contentDocument, $contentTemplate = null)
    {
        if (in_array($request->getMethod(), array('PUT', 'POST'))) {
            if (!$this->isGranted()) {
                throw new AccessDeniedException();
            }

            $contentDocument->setRegions($request->request->get('content'));
            $this->om->flush();
            return new Response("", 200, array('Content-Type'=>'application/json'));
        }

        $contentTemplate = $this->isGranted() && !$request->query->get('mercury_frame') ? "KoalaMercuryContentBundle:Content:mercury.html.twig" : $contentTemplate;

        return parent::indexAction($request, $contentDocument, $contentTemplate);
    }

    /**
     * Receive uploaded images from Mercury and return url as JSON
     */
    public function uploadAction(Request $request)
    {
        if (!$this->isGranted()) {
            throw new AccessDeniedException();
        }

        if (!$request->isXmlHttpRequest()) { // Ajax Call?
            throw new \Exception('This URL should only be called using AJAX');
        }

        $uploadedFile = $request->files->get('image')['image'];
        //$path = $this->get('kernel')->getRootDir() . "/../web/uploads/"; // TODO use config for upload dir
        $path = $this->upload['path'];
        $name = $this->getUniqueFilename($path, $uploadedFile->getClientOriginalName());
        $file = $uploadedFile->move($path, $name);

        // Return {"image": {"url": "__url__"}}
        //$image['image']['url'] = $request->getBasePath() . "/uploads/" . $name; // TODO use config for upload dir
        //$image['image']['url'] = $this->join_paths($request->getBasePath(), $this->join_paths($this->upload['dir'], $name));
        $image['image']['url'] = $this->upload['absolute'] ?
                $this->join_paths($this->upload['url'], $name) :
                $this->join_paths($request->getBasePath(), $this->join_paths($this->upload['url'], $name));

        return new Response(json_encode($image), 200, array('Content-Type'=>'application/json'));
    }

    protected function getParams(Request $request, $contentDocument)
    {
        return array(
            'page' => $contentDocument,
            'title' => $contentDocument->getTitle(),
            'regions' => $contentDocument->getRegions(),
            'save_method' => $this->save_method,
        );
    }

    /**
     * Will create unique filename in directory by appending a number to basename
     *
     * @param string $dir      directory to look in
     * @param string $filename basename to use for unique filename
     * @return string unique filename in supplied directory
     * @author Jonas Flodén
     **/
    protected function getUniqueFilename($dir, $filename)
    {
        if (!file_exists($this->join_paths($dir, $filename))) {
            return $filename;
        }

        list($name, $ext) = explode(".", $filename, 2);
        $ext = ".".$ext;
        $i=1;
        while (file_exists($this->join_paths($dir, $name . $i . $ext))) {
            $i++;
        }

        return $name . $i . $ext;
    }

    /**
     * Join two paths together
     *
     * @param string $dir1
     * @param string $dir2
     * @return string
     * @author Jonas Flodén
     */
    protected function join_paths($dir1, $dir2)
    {
        return rtrim($dir1, DIRECTORY_SEPARATOR) . "/" . ltrim($dir2, DIRECTORY_SEPARATOR);
    }

    protected function isGranted()
    {
        return empty($this->securityContext) || true === $this->securityContext->isGranted($this->requiredRole);
    }
}
