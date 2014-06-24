<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @package Aura.Auth
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Auth\Service;

use Aura\Auth\Adapter\AdapterInterface;
use Aura\Auth\Session\SessionInterface;
use Aura\Auth\Status;
use Aura\Auth\Auth;

/**
 *
 * Login handler
 *
 * @package Aura.Auth
 *
 */
abstract class AbstractService
{
    protected $adapter;

    protected $session;

    protected $auth;

    /**
     *
     *  @param Auth $auth
     *
     *  @param AdapterInterface $adapter
     *
     */
    public function __construct(
        Auth $auth,
        SessionInterface $session,
        AdapterInterface $adapter
    ) {
        $this->auth = $auth;
        $this->session = $session;
        $this->adapter = $adapter;
    }

    /**
     *
     * Forces a successful login.
     *
     * @param string $name The authenticated user name.
     *
     * @param string $data Additional arbitrary user data.
     *
     * @param string $status The new authentication status.
     *
     * @return string|false The authentication status on success, or boolean
     * false on failure.
     *
     */
    public function forceLogin(
        $name,
        array $data = array(),
        $status = Status::VALID
    ) {
        $started = $this->session->resume() || $this->session->start();
        if (! $started) {
            return false;
        }

        $this->session->regenerateId();
        $this->auth->set(
            $status,
            time(),
            time(),
            $name,
            $data
        );

        return $status;
    }

    /**
     *
     * Forces a successful logout.
     *
     * @param string $status The new authentication status.
     *
     * @return string|false The authentication status on success, or boolean
     * false on failure.
     *
     */
    public function forceLogout($status = Status::ANON)
    {
        $this->session->regenerateId();
        if (! $this->session->destroy()) {
            return false;
        }

        $this->auth->set(
            $status,
            null,
            null,
            null,
            array()
        );

        return $status;
    }
}