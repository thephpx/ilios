<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\PublishEventHandler;
use Ilios\CoreBundle\Entity\PublishEventInterface;

/**
 * Class PublishEventController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("PublishEvents")
 *
 * @deprecated
 */
class PublishEventController extends FOSRestController
{
    /**
     * Get a PublishEvent
     *
     * @ApiDoc(
     *   section = "PublishEvent",
     *   description = "Get a PublishEvent.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="PublishEvent identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\PublishEvent",
     *   statusCodes={
     *     200 = "PublishEvent.",
     *     404 = "Not Found."
     *   },
     *   deprecated = true
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $publishEvent = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $publishEvent)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['publishEvents'][] = $publishEvent;

        return $answer;
    }

    /**
     * Get all PublishEvent.
     *
     * @ApiDoc(
     *   section = "PublishEvent",
     *   description = "Get all PublishEvent.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\PublishEvent",
     *   statusCodes = {
     *     200 = "List of all PublishEvent",
     *     204 = "No content. Nothing to list."
     *   },
     *   deprecated = true
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getPublishEventHandler()
            ->findPublishEventsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['publishEvents'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a PublishEvent.
     *
     * @ApiDoc(
     *   section = "PublishEvent",
     *   description = "Create a PublishEvent.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\PublishEventType",
     *   output="Ilios\CoreBundle\Entity\PublishEvent",
     *   statusCodes={
     *     201 = "Created PublishEvent.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   },
     *   deprecated = true
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $handler = $this->getPublishEventHandler();

            $publishEvent = $handler->post(
                $this->getPostData($request),
                $request,
                $this->get('security.context')->getToken()->getUser()
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $publishEvent)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getPublishEventHandler()->updatePublishEvent($publishEvent, true, false);

            $answer['publishEvents'] = [$publishEvent];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return PublishEventInterface $publishEvent
     */
    protected function getOr404($id)
    {
        $publishEvent = $this->getPublishEventHandler()
            ->findPublishEventBy(['id' => $id]);
        if (!$publishEvent) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $publishEvent;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('publishEvent');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return PublishEventHandler
     */
    protected function getPublishEventHandler()
    {
        return $this->container->get('ilioscore.publishevent.handler');
    }
}