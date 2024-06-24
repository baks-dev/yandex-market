<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

use BaksDev\Yandex\Market\Repository\YaMarketTokenByProfile\YaMarketTokenByProfileInterface;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use DomainException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;

abstract class YandexMarket
{
    protected LoggerInterface $logger;

    protected ?UserProfileUid $profile = null;

    private ?YaMarketAuthorizationToken $AuthorizationToken = null;

    private YaMarketTokenByProfileInterface $TokenByProfile;

    private array $headers;

    public function __construct(
        YaMarketTokenByProfileInterface $TokenByProfile,
        LoggerInterface $yandexMarketLogger,
    )
    {
        $this->TokenByProfile = $TokenByProfile;
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

    public function TokenHttpClient(YaMarketAuthorizationToken $AuthorizationToken = null): RetryableHttpClient
    {
        if($AuthorizationToken !== null)
        {
            $this->AuthorizationToken = $AuthorizationToken;
            $this->profile = $AuthorizationToken->getProfile();
        }

        if($this->AuthorizationToken === null)
        {
            if(!$this->profile)
            {
                $this->logger->critical('Не указан идентификатор профиля пользователя через вызов метода profile', [__FILE__.':'.__LINE__]);

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

        $this->headers = ['Authorization' => 'Bearer '.$this->AuthorizationToken->getToken()];

        return new RetryableHttpClient(
            HttpClient::create(['headers' => $this->headers])
                ->withOptions([
                    'base_uri' => 'https://api.partner.market.yandex.ru',
                    'verify_host' => false
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

    protected function getCompany(): int
    {
        return $this->AuthorizationToken->getCompany();
    }

    protected function getCurlHeader(): string
    {
        $this->headers['accept'] = 'application/json';
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';

        return '-H "'.implode('" -H "', array_map(
                function($key, $value) {
                    return "$key: $value";
                },
                array_keys($this->headers),
                $this->headers
            )).'"';
    }

}
