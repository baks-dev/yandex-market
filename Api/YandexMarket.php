<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Yandex\Market\Api;

use BaksDev\Core\Cache\AppCacheInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Repository\YaMarketTokenByProfile\YaMarketTokenByProfileInterface;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\Cache\CacheInterface;

abstract class YandexMarket
{
    protected readonly LoggerInterface $logger;

    protected ?UserProfileUid $profile = null;

    private YaMarketAuthorizationToken|false $AuthorizationToken = false;

    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $environment,
        private readonly YaMarketTokenByProfileInterface $TokenByProfile,
        private readonly AppCacheInterface $cache,
        LoggerInterface $yandexMarketLogger,
    )
    {
        $this->logger = $yandexMarketLogger;
    }


    public function profile(UserProfileUid|string $profile): self
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;

        $this->AuthorizationToken = $this->TokenByProfile->getToken($this->profile);

        return $this;
    }

    public function TokenHttpClient(YaMarketAuthorizationToken|false $AuthorizationToken = false): RetryableHttpClient
    {
        if($AuthorizationToken !== false)
        {
            $this->AuthorizationToken = $AuthorizationToken;
            $this->profile = $AuthorizationToken->getProfile();
        }

        if($this->AuthorizationToken === false)
        {
            if(!$this->profile)
            {
                $this->logger->critical('Не указан идентификатор профиля пользователя через вызов метода profile', [self::class.':'.__LINE__]);

                throw new InvalidArgumentException(
                    'Не указан идентификатор профиля пользователя через вызов метода profile: ->profile($UserProfileUid)'
                );
            }

            $this->AuthorizationToken = $this->TokenByProfile->getToken($this->profile);

            if(!$this->AuthorizationToken)
            {
                throw new DomainException(sprintf('Токен авторизации Yandex Market не найден: %s', $this->profile));
            }
        }

        $headers['Authorization'] = sprintf('Bearer %s', $this->AuthorizationToken->getToken());

        return new RetryableHttpClient(
            HttpClient::create(['headers' => $headers])
                ->withOptions([
                    'base_uri' => 'https://api.partner.market.yandex.ru',
                    'verify_host' => false,
                ])
        );
    }

    /**
     * Profile
     */
    protected function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }

    protected function getBusiness(): int
    {
        return $this->AuthorizationToken->getBusiness();
    }

    /** Возвращает основной идентификатор компании */
    public function getCompany(): int
    {
        return $this->AuthorizationToken->getCompany();
    }

    /** Присваивает дополнительный идентификатор компании (EXTRA) */
    public function setExtraCompany(int $company): self
    {
        $this->AuthorizationToken->setExtraCompany($company);
        return $this;
    }


    public function getPercent(float|int $price): int|float
    {
        $percent = $this->AuthorizationToken->getPercent();

        if($percent === 0)
        {
            return 0;
        }

        return ($price / 100 * $percent);
    }

    /**
     * Метод проверяет что окружение является PROD,
     * тем самым позволяет выполнять операции запроса на сторонний сервис
     * ТОЛЬКО в PROD окружении
     */
    protected function isExecuteEnvironment(): bool
    {
        return $this->environment === 'prod';
    }

    protected function getCacheInit(string $namespace): CacheInterface
    {
        return $this->cache->init($namespace);
    }
}
