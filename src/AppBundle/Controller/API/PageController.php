<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Log;
use AppBundle\Entity\Page;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page Controller
 *
 * @Rest\NamePrefix("api_page_")
 * @Rest\Prefix("/v1/pages")
 */
class PageController extends FOSRestController
{
    use ControllerHelperTrait;

    /**
     * Повертає колекцію списку всіх публакцій
     *
     * @ApiDoc(
     *     description="Список всіх публікацій",
     *     section="Page",
     *     statusCodes={
     *          200="Returned when successful",
     *          500="Returned when internal error on the server occurred"
     *      }
     * )
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher
     *
     * @return Response
     *
     * @Rest\QueryParam(name="_limit",  requirements="\d+", nullable=true, strict=true, description="Limit", default="20")
     * @Rest\QueryParam(name="_offset", requirements="\d+", nullable=true, strict=true, description="Offset", default="0")
     * @Rest\QueryParam(name="type", nullable=true, description="Фільтр по типу публцікації. Можливі типу: new_page - сторінка не змінювалась, changed_page - сторінка змінювалась, deleted_page - сторінка видалена")
     *
     * @Rest\Get("")
     *
     * @View(serializerGroups="page")
     */
    public function getListAction(ParamFetcherInterface $paramFetcher)
    {
        try {
            $filters = $paramFetcher->all();
            $repository = $this->getDoctrine()->getRepository(Page::class);
            $pages = $this->get('app.matching')->matching($repository, $filters);

            $view = $this->createViewForHttpOkResponse([
                '_metadata' => [
                    'total' => $repository->getTotalNumberOfPages(),
                    '_limit' => (int) $filters['_limit'],
                    '_offset' => (int) $filters['_offset'],
                ],
                'pages' => $pages,
            ]);
        } catch (\Exception $e) {
            throw $this->createInternalServerErrorException();
        }

        return $this->handleView($view);
    }

    /**
     * Повертає публікацію
     *
     * @param Page $page $page
     *
     * @return Response
     *
     * @ApiDoc(
     *     description="Повертає публікацію",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\w+", "description"="ID of page"}
     *      },
     *     section="Page",
     *     output={
     *          "class"="AppBundle\Entity\Page",
     *           "groups"={"page"}
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when page not found",
     *          500="Returned when internal error on the server occurred"
     *      }
     * )
     *
     * @Rest\Get("/{id}")
     *
     * @View(serializerGroups="page")
     */
    public function getAction(Page $page)
    {
        try {
            $view = $this->createViewForHttpOkResponse([
                'page' => $page,
            ]);
        } catch (\Exception $e) {
            throw $this->createInternalServerErrorException();
        }

        return $this->handleView($view);
    }

    /**
     * Повертає версії публікацій
     *
     * ### logged_at - дати, коли була зміна контенту.
     * ### version - номер версії публікації.###
     * ### data - текст публікації
     *
     * @param Page $page $page
     *
     * @return Response
     *
     * @ApiDoc(
     *     description="Повертає версії публікацій",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\w+", "description"="ID of page"}
     *      },
     *     section="Page",
     *     output={
     *          "class"="AppBundle\Entity\Log",
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when page not found",
     *          500="Returned when internal error on the server occurred"
     *      }
     * )
     *
     * @Rest\Get("/{id}/revision")
     */
    public function revisionAction(Page $page)
    {
        try {
            $logRepository = $this->getDoctrine()->getRepository(Log::class);
            $logs = $logRepository->findRevisionsByPage($page);

            $view = $this->createViewForHttpOkResponse([
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            throw $this->createInternalServerErrorException();
        }

        return $this->handleView($view);
    }

    /**
     * Повертає різницю між версію публікацій
     *
     * @param Page    $page        $page
     * @param integer $oldRevision Old revision
     * @param integer $newRevision New revision
     *
     * @return Response
     *
     * @ApiDoc(
     *     description="Повертає різницю між версіями публікацій",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "description"="ID of page"},
     *          {"name"="oldRevision", "dataType"="integer", "description"="Number of old revision"},
     *          {"name"="newRevision", "dataType"="integer", "description"="Number of new revision"}
     *      },
     *     section="Page",
     *     output={
     *          "class"="AppBundle\Entity\Log",
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when page not found",
     *          500="Returned when internal error on the server occurred"
     *      }
     * )
     *
     * @Rest\Get("/{id}/revision/diff/{oldRevision}/{newRevision}")
     */
    public function diffAction(Page $page, $oldRevision, $newRevision)
    {
        $logRepository = $this->getDoctrine()->getRepository(Log::class);
        try {
            $pageOldRevision = $logRepository->findRevisionByPageAndVersion($page, $oldRevision);
            $pageNewRevision = $logRepository->findRevisionByPageAndVersion($page, $newRevision);

            $differ = new Differ();

            $view = $this->createViewForHttpOkResponse([
                'changes' => $differ->diff($pageOldRevision->getData()['content'], $pageNewRevision->getData()['content']),
            ]);
        } catch (\Exception $e) {
            throw $this->createInternalServerErrorException();
        }

        return $this->handleView($view);
    }
}
