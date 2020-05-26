<?php

namespace App\EventListeners;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable()->getMessage();
        $this->logger->error($e);

    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }
}
