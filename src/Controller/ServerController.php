<?php

namespace App\Controller;

use App\Entity\Server;
use App\Entity\Slave;
use App\Form\ServerType;
use App\Services\ServerService;
use App\Services\SlaveService;
use Kilik\TableBundle\Components\Column;
use Kilik\TableBundle\Components\Filter;
use Kilik\TableBundle\Components\Table;
use Kilik\TableBundle\Services\TableService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/server")
 */
class ServerController extends AbstractController
{
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
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
    public function view(Server $server, SlaveService $slaveService)
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

    /**
     * @Route("/{server}/channels/status", name="server_slaves_status_ajax", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function slavesStatusRender(Server $server, SlaveService $slaveService): JsonResponse
    {
        try {
            $slaveStatuses = $slaveService->showSlaveStatus($server);
        } catch (\Exception $e) {
            $this->logger->error('Error while render slaves status', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse(
            [
                'html' => $this->render('server/channels_status_ajax.html.twig', ['slaveStatuses' => $slaveStatuses])->getContent(),
            ]
        );
    }

    /**
     * @Route("/{server}/slaves/start", name="server_slaves_start_ajax", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function startSlaves(Server $server, ServerService $serverService): JsonResponse
    {
        try {
            $serverService->startSlaves($server);
        } catch (\Exception $e) {
            $this->logger->error('Error while start slaves', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{server}/slaves/stop", name="server_slaves_stop_ajax", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function stopSlaves(Server $server, ServerService $serverService): JsonResponse
    {
        try {
            $serverService->stopSlaves($server);
        } catch (\Exception $e) {
            $this->logger->error('Error while stop slaves', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{server}/slave/channel/{channel}/start", name="server_slave_channel_start_ajax", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function startSlaveForChannel(Server $server, int $channel, SlaveService $slaveService): JsonResponse
    {
        $slave = $this->getDoctrine()->getRepository(Slave::class)->findOneBy(
            ['server' => $server, 'channelName' => $channel]
        );

        if ($slave === null) {
            $this->logger->error('slave for server '.$server->getName().' and channel '.$channel.' not found', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
            ]);
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        try {
            $slaveService->startSlave($slave);
        } catch (\Exception $e) {
            $this->logger->error('Error while start slave for channel '.$channel, [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{server}/slave/channel/{channel}/stop", name="server_slave_channel_stop_ajax", methods={"GET"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function stopSlaveForChannel(Server $server, int $channel, SlaveService $slaveService): JsonResponse
    {
        $slave = $this->getDoctrine()->getRepository(Slave::class)->findOneBy(
            ['server' => $server, 'channelName' => $channel]
        );

        if ($slave === null) {
            $this->logger->error('slave for server '.$server->getName().' and channel '.$channel.' not found', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
            ]);
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        try {
            $slaveService->stopSlave($slave);
        } catch (\Exception $e) {
            $this->logger->error('Error while stop slave for channel '.$channel, [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{server}/slave/channel/{channel}/switch-next-master-log-file", name="server_slave_channel_switch_next_master_log_file_ajax", methods={"POST"})
     * @ParamConverter("server", options={"mapping": {"server" : "name"}})
     *
     * @return JsonResponse
     */
    public function switchSlaveToNextMasterLogFileForChannel(Request $request, Server $server, int $channel, SlaveService $slaveService): JsonResponse
    {
        $nextMasterLogFile = $request->get('nextMasterLogFile');

        if ($nextMasterLogFile === null) {
            $this->logger->error('Cannot switch to next position if MasterLogFile is not defined', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
            ]);
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $slave = $this->getDoctrine()->getRepository(Slave::class)->findOneBy(
            ['server' => $server, 'channelName' => $channel]
        );

        if ($slave === null) {
            $this->logger->error('slave for server '.$server->getName().' and channel '.$channel.' not found', [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
            ]);
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        try {
            $slaveService->switchToNextMasterLogFile($slave, $channel, $nextMasterLogFile);
        } catch (\Exception $e) {
            $this->logger->error('Error while switch slave to next master log file for channel '.$channel, [
                'server_id' => $server->getId(),
                'server_name' => $server->getName(),
                'channel' => $channel,
                'next_master_log_file' => $nextMasterLogFile,
                'next_position' => 0,
                'exception' => $e->getMessage(),
            ]);
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
