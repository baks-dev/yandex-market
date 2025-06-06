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
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;

final class YaMarketTokenCurrentEventRepository implements YaMarketTokenCurrentEventInterface
{
    private YaMarketTokenUid|false $main;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forMain(YaMarketToken|YaMarketTokenUid $main): self
    {
        if($main instanceof YaMarketToken)
        {
            $main = $main->getId();
        }

        $this->main = $main;

        return $this;
    }

    /** Метод возвращает активное событие токена профиля */
    public function find(): YaMarketTokenEvent|false
    {
        if(false === ($this->main instanceof YaMarketTokenUid))
        {
            throw new \InvalidArgumentException('Invalid Argument YaMarketToken');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(YaMarketToken::class, 'main')
            ->where('main.id = :main')
            ->setParameter(
                key: 'main',
                value: $this->main,
                type: YaMarketTokenUid::TYPE);


        $orm
            ->select('event')
            ->join(
                YaMarketTokenEvent::class,
                'event',
                'WITH',
                'event.id = main.event',
            );

        return $orm->getQuery()->getOneOrNullResult() ?: false;
    }
}
