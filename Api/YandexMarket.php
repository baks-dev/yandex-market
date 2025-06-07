<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Repository\YaMarketToken\YaMarketTokenByProfileInterface;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\Cache\CacheInterface;

abstract class YandexMarket
{
    private YaMarketTokenUid|false $identifier = false;

    private YaMarketAuthorizationToken|false $AuthorizationToken = false;

    //private YaMarketTokenUid|false $identifier = false;

    //protected UserProfileUid|false $profile = false;


    public function __construct(
        #[Autowire(env: 'APP_ENV')] private readonly string $environment,
        #[Target('yandexMarketLogger')] protected readonly LoggerInterface $logger,
        private readonly YaMarketTokenByProfileInterface $TokenByProfile,
        private readonly AppCacheInterface $cache,
    ) {}

    public function forTokenIdentifier(YaMarketToken|YaMarketTokenUid $identifier): self
    {
        if($identifier instanceof YaMarketToken)
        {
            $identifier = $identifier->getId();
        }

        $this->AuthorizationToken = $this->TokenByProfile
            ->forToken($identifier)
            ->find();

        $this->identifier = $identifier;

        return $this;
    }


    /** @deprecated */
    public function profile(): self
    {
        return $this;
    }

    public function TokenHttpClient(YaMarketAuthorizationToken|false $AuthorizationToken = false): RetryableHttpClient
    {
        if($AuthorizationToken instanceof YaMarketAuthorizationToken)
        {
            $this->AuthorizationToken = $AuthorizationToken;
        }

        if(false === ($this->AuthorizationToken instanceof YaMarketAuthorizationToken))
        {
            if(false === ($this->identifier instanceof YaMarketTokenUid))
            {
                $this->logger->critical('Не указан идентификатор токена через вызов метода forTokenIdentifier', [self::class.':'.__LINE__]);

                throw new InvalidArgumentException(
                    'Не указан идентификатор токена через вызов метода profile: ->forTokenIdentifier($UserProfileUid)',
                );
            }

            $this->AuthorizationToken = $this->TokenByProfile
                ->forToken($this->identifier)
                ->find();

            if(false === ($this->AuthorizationToken instanceof YaMarketAuthorizationToken))
            {
                throw new DomainException(sprintf('Токен авторизации Yandex Market не найден: %s', $this->identifier));
            }
        }

        $headers['Api-Key'] = $this->AuthorizationToken->getToken();

        return new RetryableHttpClient(
            HttpClient::create(['headers' => $headers])
                ->withOptions([
                    'base_uri' => 'https://api.partner.market.yandex.ru',
                    'verify_host' => false,
                ]),
        );
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

    /**
     * Profile
     */

    protected function getProfile(): ?UserProfileUid
    {
        return $this->AuthorizationToken->getProfile();
    }

    protected function getBusiness(): int
    {
        return $this->AuthorizationToken->getBusiness();
    }

    /** Возвращает основной идентификатор компании */
    protected function getCompany(): int
    {
        return $this->AuthorizationToken->getCompany();
    }

    protected function getPercent(): string
    {
        return $this->AuthorizationToken->getPercent();
    }

    protected function getVat(): string|false
    {
        return $this->AuthorizationToken->getVat();
    }

    protected function isCard(): bool
    {
        return $this->AuthorizationToken->isCard();
    }

    protected function isStocks(): bool
    {
        return $this->AuthorizationToken->isStocks();
    }
}
