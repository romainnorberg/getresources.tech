<?php

namespace App\Processor;

use Algolia\SearchBundle\IndexManager;
use App\Vo\SiteHitProcessorVo;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrProcessor;
use Enqueue\Client\TopicSubscriberInterface;

class SiteHitProcessor implements PsrProcessor, TopicSubscriberInterface
{
    /* @var IndexManager */
    private $indexManager;

    /* @var $message PsrMessage */
    private $message;
    /* @var $message PsrContext */
    private $session;

    /**
     * DefaultController constructor.
     *
     * @param IndexManager           $indexManager
     */
    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    public function process(PsrMessage $message, PsrContext $session)
    {
        $this->message = $message;
        $this->session = $session;

        try {
            $this->execute();
        } catch (\Exception $e) {
            echo var_dump($message);
            echo $e->getMessage() . "\n";

            return self::REJECT;
        }

        return self::ACK;
        // return self::REJECT; // when the message is broken
        // return self::REQUEUE; // the message is fine but you want to postpone processing
    }

    /**
     * @throws \Exception
     */
    private function execute()
    {
        //throw new \Exception('putain');

        $messageBody = json_decode($this->message->getBody(), true);
        $jsonError = json_last_error();

        if (null === $messageBody || $jsonError !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf('Could not decode JSON! (%s)', $jsonError));
        }

        $siteHitProcessorVo = new SiteHitProcessorVo();
        $siteHitProcessorVo->populateFromArray(json_decode($this->message->getBody(), true));

        var_dump($siteHitProcessorVo);
    }

    public static function getSubscribedTopics()
    {
        return ['aSiteHitTopic'];
    }
}