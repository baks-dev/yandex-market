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

namespace BaksDev\Yandex\Market\Repository\YaMarketTokensByProfile;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\Active\YaMarketTokenActive;
use BaksDev\Yandex\Market\Entity\Event\Profile\YaMarketTokenProfile;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use Generator;

final readonly class YaMarketTokensByProfileRepository implements YaMarketTokensByProfileInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод возвращает идентификаторы токенов профиля пользователя
     *
     * @return  Generator<int, YaMarketTokenUid>|false $var
     */
    public function findAll(UserProfile|UserProfileUid $profile): Generator|false
    {
        if($profile instanceof UserProfile)
        {
            $profile = $profile->getId();
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('token.id AS value')
            ->from(YaMarketToken::class, 'token');

        $dbal->join(
            'token',
            YaMarketTokenProfile::class,
            'profile',
            'profile.event = token.event AND profile.value = :profile',
        )
            ->setParameter(
                key: 'profile',
                value: $profile,
                type: UserProfileUid::TYPE,
            );

        $dbal->join(
            'token',
            YaMarketTokenActive::class,
            'active',
            'active.event = token.event AND active.value IS TRUE',
        );

        return $dbal
            ->enableCache('yandex-market')
            ->fetchAllHydrate(YaMarketTokenUid::class);
    }
}