<?php

namespace App\Controller;

use App\Entity\Cluster;
use App\Form\ClusterType;
use Kilik\TableBundle\Components\Column;
use Kilik\TableBundle\Components\Filter;
use Kilik\TableBundle\Components\Table;
use Kilik\TableBundle\Services\TableService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/cluster")
 */
class ClusterController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    private function getTable()
    {
        $queryBuilder = $this->getDoctrine()->getRepository(Cluster::class)->createQueryBuilder('c')
            ->select('c, GROUP_CONCAT(DISTINCT s.name) as servers')
            ->leftJoin('c.servers', 's')
            ->groupBy('c');

        $table = (new Table())
            ->setId('cluster_list')
            ->setPath($this->generateUrl('cluster_list_ajax'))
            ->setQueryBuilder($queryBuilder, 'c')
            ->setTemplate('cluster/_list.html.twig');

        $table->addColumn(
            (new Column())
                ->setLabel('field.name.label')
                ->setTranslateLabel(true)
                ->setSort(['c.name' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('c.name')
                        ->setName('c_name')
                )
        );

        $table->addColumn(
            (new Column())
                ->setLabel(ucfirst($this->translator->trans('field.server.labels')))
                ->setFilter(
                    (new Filter())
                        ->setField('servers')
                        ->setName('servers')
                        ->setHaving(true)
                )
        );

        return $table;
    }

    /**
     * @Route("/list", name="cluster_list")
     *
     * @Template()
     */
    public function list(TableService $tableService)
    {
        return [
            'table' => $tableService->createFormView($this->getTable()),
        ];
    }

    /**
     * @Route("/list/ajax", name="cluster_list_ajax")
     */
    public function _list(Request $request, TableService $tableService)
    {
        return $tableService->handleRequest($this->getTable(), $request);
    }

    /**
     * @Route("/add", name="cluster_add")
     *
     * @return array|RedirectResponse
     * @Template("cluster/edit.html.twig")
     *
     */
    public function add(Request $request)
    {
        $cluster = new Cluster();
        $form = $this->createForm(ClusterType::class, $cluster);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($cluster);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->translator->trans('message.clusterSaved.success'));

            return new RedirectResponse($this->generateUrl('cluster_view', ['cluster' => $cluster->getName()]));
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => ucfirst($this->translator->trans('action.add')),
        ];
    }

    /**
     * @Route("/edit/{cluster}", name="cluster_edit")
     * @ParamConverter("cluster", options={"mapping": {"cluster" : "name"}})
     *
     * @return array|RedirectResponse
     * @Template()
     *
     */
    public function edit(Request $request, Cluster $cluster)
    {
        $form = $this->createForm(ClusterType::class, $cluster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('message.clusterSaved.success'));

            return new RedirectResponse($this->generateUrl('cluster_view', ['cluster' => $cluster->getName()]));
        }

        return [
            'cluster' => $cluster,
            'form' => $form->createView(),
            'pageTitle' => ucfirst($this->translator->trans('action.edit')),
        ];
    }

    /**
     * @Route("/{cluster}", name="cluster_view", methods={"GET"})
     * @ParamConverter("cluster", options={"mapping": {"cluster" : "name"}})
     *
     * @return array
     * @Template()
     */
    public function view(Cluster $cluster)
    {
        return [
            'cluster' => $cluster,
        ];
    }

    /**
     * @Route("/delete/{cluster}", name="cluster_delete")
     * @ParamConverter("cluster", options={"mapping": {"cluster" : "name"}})
     *
     * @return array|RedirectResponse
     * @Template("components/_delete_modal.html.twig")
     */
    public function _delete(Request $request, Cluster $cluster)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('cluster_delete', ['cluster' => $cluster->getName()]))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->remove($cluster);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('message.clusterDelete.success'));
            return $this->redirectToRoute('cluster_list');
        }

        return [
            'cluster' => $cluster,
            'form' => $form->createView(),
            'isDeletable' => true,
            'deleteConfirmationText' => $this->translator->trans('message.clusterDeleteConfirmation'),
        ];
    }
}
