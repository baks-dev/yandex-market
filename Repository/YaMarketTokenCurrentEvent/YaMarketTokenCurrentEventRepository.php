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

namespace BaksDev\Yandex\Market\Repository\YaMarketTokenCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;

final readonly class YaMarketTokenCurrentEventRepository implements YaMarketTokenCurrentEventInterface
{
    public function __construct(private ORMQueryBuilder $ORMQueryBuilder) {}

    /** Метод возвращает активное событие токена профиля */
    public function findByProfile(UserProfileUid|string $profile): YaMarketTokenEvent|false
    {
        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);


        $orm
            ->from(YaMarketToken::class, 'main')
            ->where('main.id = :profile')
            ->setParameter('profile', $profile, UserProfileUid::TYPE);


        $orm
            ->select('event')
            ->join(
                YaMarketTokenEvent::class,
                'event',
                'WITH',
                'event.id = main.event'
            );

        return $orm->getQuery()->getOneOrNullResult() ?: false;
    }
}
