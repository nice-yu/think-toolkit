<?php
declare(strict_types=1);


namespace NiceYu\Contract;


/**
 * Interface UserProviderInterface
 * @package app\contract
 */
interface UserProviderInterface
{
    /**
     * 是否开启安全模式
     * @param array $defaults
     * @return bool
     */
    public function supports(array $defaults): bool;

    /**
     * 获取凭证
     * @return string
     */
    public function getCredentials(): string;

    /**
     * 得到用户信息
     * @param string $credentials
     * @return object
     */
    public function getUser(string $credentials):object;
}