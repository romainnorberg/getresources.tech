<?php

namespace App\Infrastructure\Session;

use Predis\Client;

class RedisHandler implements \SessionHandlerInterface
{
    /**
     * @var Client|\Redis
     */
    protected $redis;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var integer Default PHP max execution time in seconds
     */
    public const DEFAULT_MAX_EXECUTION_TIME = 30;

    /**
     * @var boolean Indicates an sessions should be locked
     */
    private $locking;

    /**
     * @var boolean Indicates an active session lock
     */
    private $locked;

    /**
     * @var string Session lock key
     */
    private $lockKey;

    /**
     * @var integer Microseconds to wait between acquire lock tries
     */
    private $spinLockWait;

    /**
     * @var integer Maximum amount of seconds to wait for the lock
     */
    private $lockMaxWait;


    public function __construct($redisHost, $redisPassword = null, $redisPort, $redisScheme, $ttl = 1200, $prefix = 'session', $locking = true, $spinLockWait = 150000)
    {
        $clientConfig = [
            'scheme' => $redisScheme,
            'host'   => $redisHost,
            'port'   => $redisPort,
        ];

        if (!empty($redisPassword)) {
            $clientConfig['password'] = $redisPassword;
        }

        //Setup the client
        $this->redis = new Client($clientConfig);

        $this->ttl = $ttl;
        $this->prefix = $prefix;
        $this->locking = $locking;
        $this->locked = false;
        $this->spinLockWait = $spinLockWait;
        $this->lockMaxWait = ini_get('max_execution_time');
        if (!$this->lockMaxWait) {
            $this->lockMaxWait = self::DEFAULT_MAX_EXECUTION_TIME;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function open($savePath, $sessionName): bool
    {
        return true;
    }


    private function lockSession($sessionId)
    {
        $attempts = (1000000 / $this->spinLockWait) * $this->lockMaxWait;

        $this->lockKey = $sessionId . '.lock';
        for ($i = 0; $i < $attempts; $i++) {
            $success = $this->redis->setnx($this->prefix . $this->lockKey, '1');
            if ($success) {
                $this->locked = true;
                $this->redis->expire($this->prefix . $this->lockKey, $this->lockMaxWait + 1);

                return true;
            }
            usleep($this->spinLockWait);
        }

        return false;
    }

    private function unlockSession()
    {
        $this->redis->del([$this->prefix . $this->lockKey]);
        $this->locked = false;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        if ($this->locking && $this->locked) {
            $this->unlockSession();
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($sessionId)
    {
        if ($this->locking && !$this->locked && !$this->lockSession($sessionId)) {
            return false;
        }

        return $this->redis->get($this->getRedisKey($sessionId)) ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function write($sessionId, $data): bool
    {
        if (0 < $this->ttl) {
            $this->redis->setex($this->getRedisKey($sessionId), $this->ttl, $data);
        } else {
            $this->redis->set($this->getRedisKey($sessionId), $data);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($sessionId): bool
    {
        $this->redis->del([$this->getRedisKey($sessionId)]);
        $this->close();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime): bool
    {
        return true;
    }

    /**
     * Change the default TTL
     *
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Prepends the session ID with a user-defined prefix (if any).
     *
     * @param string $sessionId session ID
     *
     * @return string prefixed session ID
     */
    protected function getRedisKey($sessionId): string
    {
        if (empty($this->prefix)) {
            return $sessionId;
        }

        return $this->prefix . ':' . $sessionId;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }

}