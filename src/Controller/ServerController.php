<?php

namespace App\Controller;

use App\Entity\Server;
use App\Form\ServerType;
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
 * @Route("/server")
 */
class ServerController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    private function getTable()
    {
        $queryBuilder = $this->getDoctrine()->getRepository(Server::class)->createQueryBuilder('s')
            ->select('s, c')
            ->leftJoin('s.cluster', 'c');

        $table = (new Table())
            ->setId('server_list')
            ->setPath($this->generateUrl('server_list_ajax'))
            ->setQueryBuilder($queryBuilder, 's')
            ->setTemplate('server/_list.html.twig');

        $table->addColumn(
            (new Column())
                ->setLabel('field.name.label')
                ->setTranslateLabel(true)
                ->setSort(['s.name' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('s.name')
                        ->setName('s_name')
                )
        );

        $table->addColumn(
            (new Column())
                ->setLabel('field.cluster.label')
                ->setTranslateLabel(true)
                ->setSort(['c.name' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('c.name')
                        ->setName('c_name')
                )->setDisplayCallback(function ($name) {
                    if (null===$name) {
                        return '';
                    }
                    return '<a href="'.$this->generateUrl('cluster_view', ['cluster' => $name]).'">'.$name.'</a>';
                })->setRaw(true)
        );

        $table->addColumn(
            (new Column())
                ->setLabel('field.description.label')
                ->setTranslateLabel(true)
                ->setSort(['s.description' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('s.description')
                        ->setName('s_description')
                )
        );

        $table->addColumn(
            (new Column())
                ->setLabel('field.host.label')
                ->setTranslateLabel(true)
                ->setSort(['s.host' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('s.host')
                        ->setName('s_host')
                )
        );

        $table->addColumn(
            (new Column())
                ->setLabel('field.port.label')
                ->setTranslateLabel(true)
                ->setSort(['s.port' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('s.port')
                        ->setName('s_port')
                )
        );

        $table->addColumn(
            (new Column())
                ->setLabel('field.user.label')
                ->setTranslateLabel(true)
                ->setSort(['s.user' => 'asc'])
                ->setFilter(
                    (new Filter())
                        ->setField('s.user')
                        ->setName('s_user')
                )
        );

        return $table;
    }

    /**
     * @Route("/list", name="server_list")
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
     * @Route("/list/ajax", name="server_list_ajax")
     */
    public function _list(Request $request, TableService $tableService)
    {
        return $tableService->handleRequest($this->getTable(), $request);
    }

    /**
     * @Route("/add", name="server_add")
     *
     * @return array|RedirectResponse
     * @Template("server/edit.html.twig")
     *
     */
    public function add(Request $request)
    {
        $server = new Server();
        $form = $this->createForm(ServerType::class, $server);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password-edit')->getData() !== null) {
                $server->setPassword($form->get('password-edit')->getData());
            }
            $this->getDoctrine()->getManager()->persist($server);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->translator->trans('message.serverSaved.success'));

            return new RedirectResponse($this->generateUrl('server_view', ['server' => $server->getName()]));
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => ucfirst($this->translator->trans('action.add')),
        ];
    }

    /**
     * @Route("/edit/{server}", name="server_edit")
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return array|RedirectResponse
     * @Template()
     *
     */
    public function edit(Request $request, Server $server)
    {
        $form = $this->createForm(ServerType::class, $server);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password-edit')->getData() !== null) {
                $server->setPassword($form->get('password-edit')->getData());
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('message.serverSaved.success'));

            return new RedirectResponse($this->generateUrl('server_view', ['server' => $server->getName()]));
        }

        return [
            'server' => $server,
            'form' => $form->createView(),
            'pageTitle' => ucfirst($this->translator->trans('action.edit')),
        ];
    }

    /**
     * @Route("/{server}", name="server_view", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return array
     * @Template()
     */
    public function view(Server $server)
    {
        return [
            'server' => $server,
        ];
    }

    /**
     * @Route("/delete/{server}", name="server_delete")
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return array|RedirectResponse
     * @Template("components/_delete_modal.html.twig")
     */
    public function _delete(Request $request, Server $server)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('server_delete', ['server' => $server->getName()]))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->remove($server);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('message.serverDelete.success'));
            return $this->redirectToRoute('server_list');
        }

        return [
            'server' => $server,
            'form' => $form->createView(),
            'isDeletable' => true,
            'deleteConfirmationText' => $this->translator->trans('message.serverDeleteConfirmation'),
        ];
    }
}
