<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Server;

use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Trillium\Server\Exception\InvalidMessageException;

/**
 * Ws Class
 *
 * @package Trillium\Server
 */
class Ws implements MessageComponentInterface
{

    /**
     * @var array List of threads and subscribers
     */
    private $threads;

    /**
     * @var array List of subscribers and threads
     */
    private $clients;

    /**
     * @var LoggerInterface|null A logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger A logger
     *
     * @return self
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->threads = [];
        $this->clients = [];
        $this->logger  = $logger;
    }

    /**
     * {@inheritdoc}
     * @param ConnectionInterface|\StdClass $conn Connection
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->log('debug', sprintf('Open connection: %s', $conn->resourceId));
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level   Level
     * @param string $message Message
     * @param array  $context Context
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    private function log($level, $message, array $context = [])
    {
        if ($this->logger !== null) {
            if (!method_exists($this->logger, $level)) {
                throw new \RuntimeException(sprintf('Method %s does not exists in the logger instance', $level));
            }
            call_user_func([$this->logger, $level], $message, $context);
        }
    }

    /**
     * {@inheritdoc}
     * @param ConnectionInterface|\StdClass $conn Connection
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->log('debug', sprintf('Close connection: %s', $conn->resourceId));
        $thread = isset($this->clients[$conn->resourceId]) ? $this->clients[$conn->resourceId] : false;
        if ($thread !== false) {
            unset($this->threads[$thread][$conn->resourceId]);
            unset($this->clients[$conn->resourceId]);
        }
    }

    /**
     * {@inheritdoc}
     * @param ConnectionInterface|\StdClass $conn Connection
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log('error', $e->getMessage(), $e->getTrace());
        $conn->close();
    }

    /**
     * {@inheritdoc}
     * @param ConnectionInterface|\StdClass $from Connection
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->log('debug', sprintf('New message from: %s', $from->resourceId), ['message' => $msg]);
        $msg    = $this->validateMessage($msg);
        $thread = (int) $msg['value'];
        if ($msg['action'] == 'subscribe') {
            $oldThread = isset($this->clients[$from->resourceId]) ? $this->clients[$from->resourceId] : false;
            if ($oldThread !== false) {
                unset($this->threads[$oldThread][$from->resourceId]);
            }
            if (!isset($this->threads[$thread])) {
                $this->threads[$thread] = [];
            }
            $this->threads[$thread][$from->resourceId] = $from;
            $this->clients[$from->resourceId]          = $thread;
            $this->log('debug', sprintf('Client %s is subscribed to thread %s', $from->resourceId, $thread));
        } else {
            throw new \RuntimeException(sprintf('Unknown action: %s', $msg['action']));
        }
    }

    /**
     * Validates a message
     *
     * @param string $message JSON string
     *
     * @throws InvalidMessageException
     *
     * @return array
     */
    private function validateMessage($message)
    {
        $message = json_decode($message, true);
        $error   = '';
        if ($message === null) {
            $error = json_last_error_msg();
            if ($error === null) {
                $error = 'Syntax error, malformed JSON';
            }
        } elseif (!array_key_exists('action', $message)) {
            $error = 'Action does not exists';
        } elseif (!array_key_exists('value', $message)) {
            $error = 'Value does not exists';
        }
        if (!empty($error)) {
            throw new InvalidMessageException($error);
        }

        return $message;
    }

    /**
     * Sends new post to all subscribers
     *
     * @param string $message A message
     *
     * @throws InvalidMessageException
     *
     * @return void
     */
    public function onNewPost($message)
    {
        $message = $this->validateMessage($message);
        if ($message['action'] !== 'new_post') {
            throw new InvalidMessageException(sprintf('Unknown action: %s', $message['action']));
        }
        if (!is_array($message['value'])) {
            throw new InvalidMessageException('Value is not array');
        }
        if (sizeof(array_diff(array_keys($message['value']), ['thread', 'post', 'image'])) > 0) {
            throw new InvalidMessageException('Wrong items given');
        }
        $thread = $message['value']['thread'];
        if (!empty($this->threads[$thread])) {
            $this->log('debug', 'Send a new post to all subscribers...', ['thread' => $thread]);
            /** @var $conn ConnectionInterface */
            foreach ($this->threads[$thread] as $conn) {
                $conn->send(json_encode(['action' => 'receive', 'value' => $message['value']]));
            }
        } else {
            $this->log('debug', 'Subscribers for this thread are not exists', ['thread' => $thread]);
        }
    }

}
