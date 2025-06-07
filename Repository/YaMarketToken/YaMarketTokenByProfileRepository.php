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

namespace BaksDev\Yandex\Market\Repository\YaMarketToken;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Yandex\Market\Entity\Event\Active\YaMarketTokenActive;
use BaksDev\Yandex\Market\Entity\Event\Business\YaMarketTokenBusiness;
use BaksDev\Yandex\Market\Entity\Event\Card\YaMarketTokenCard;
use BaksDev\Yandex\Market\Entity\Event\Company\YaMarketTokenCompany;
use BaksDev\Yandex\Market\Entity\Event\Percent\YaMarketTokenPercent;
use BaksDev\Yandex\Market\Entity\Event\Profile\YaMarketTokenProfile;
use BaksDev\Yandex\Market\Entity\Event\Stocks\YaMarketTokenStocks;
use BaksDev\Yandex\Market\Entity\Event\Token\YaMarketTokenValue;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use InvalidArgumentException;

final  class YaMarketTokenByProfileRepository implements YaMarketTokenByProfileInterface
{
    private YaMarketTokenUid|false $token = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function forToken(YaMarketToken|YaMarketTokenUid $token): self
    {
        if($token instanceof YaMarketToken)
        {
            $token = $token->getId();
        }

        $this->token = $token;

        return $this;
    }

    /**
     * Метод возвращает токен авторизации профиля
     */
    public function find(): YaMarketAuthorizationToken|false
    {
        if(false === ($this->token instanceof YaMarketTokenUid))
        {
            throw new InvalidArgumentException('Invalid Argument YaMarketToken');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(YaMarketToken::class, 'token')
            ->where('token.id = :token')
            ->setParameter(
                key: 'token',
                value: $this->token,
                type: YaMarketTokenUid::TYPE,
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenProfile::class,
                'profile',
                'profile.event = token.event',
            );

        $dbal
            ->join(
                'token',
                UserProfileInfo::class,
                'info',
                'info.profile = profile.value AND info.status = :status',
            )
            ->setParameter(
                key: 'status',
                value: UserProfileStatusActive::class,
                type: UserProfileStatus::TYPE,
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenActive::class,
                'active',
                'active.event = token.event AND active.value IS TRUE',
            );


        $dbal
            ->join(
                'token',
                YaMarketTokenValue::class,
                'token_value',
                'token_value.event = token.event',
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenCompany::class,
                'company',
                'company.event = token.event',
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenBusiness::class,
                'business',
                'business.event = token.event',
            );


        $dbal
            ->join(
                'token',
                YaMarketTokenPercent::class,
                'percent',
                'percent.event = token.event',
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenCard::class,
                'card',
                'card.event = token.event',
            );

        $dbal
            ->join(
                'token',
                YaMarketTokenStocks::class,
                'stocks',
                'stocks.event = token.event',
            );

        $dbal
            ->select('profile.value AS profile')
            ->addSelect('token_value.value AS token')
            ->addSelect('company.value AS company')
            ->addSelect('business.value AS business')
            ->addSelect('percent.value AS percent')
            ->addSelect('card.value AS card')
            ->addSelect('stocks.value AS stocks');

        /* Кешируем результат ORM */
        return $dbal
            ->enableCache('yandex-market')
            ->fetchHydrate(YaMarketAuthorizationToken::class);

    }

}
