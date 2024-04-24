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

namespace BaksDev\Yandex\Market\Repository\YaMarketTokenChoice;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Repository\YaMarketTokenByProfile\YaMarketTokenByProfileInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class YaMarketTokenChoiceRepository implements YaMarketTokenChoiceInterface
{

    private YaMarketTokenByProfileInterface $wbTokenByProfile;
    private ORMQueryBuilder $ORMQueryBuilder;


    public function __construct(
        YaMarketTokenByProfileInterface $wbTokenByProfile,
        ORMQueryBuilder $ORMQueryBuilder
    )
    {
        $this->wbTokenByProfile = $wbTokenByProfile;
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /**
     * Возвращает всю коллекцию идентификаторов токенов
     */
    public function getTokenCollection(): ?array
    {
        $qb = $this->ORMQueryBuilder
            ->createQueryBuilder(self::class);

        $select = sprintf('new %s(token.id, users_profile_personal.username)', UserProfileUid::class);
        $qb->select($select);

        $qb->from(YaMarketToken::class, 'token');

        $qb->join(
            YaMarketTokenEvent::class,
            'event',
            'WITH',
            'event.id = token.event AND event.profile = token.id',
        );

        $qb->join(
            UserProfileInfo::class,
            'users_profile_info',
            'WITH',
            'users_profile_info.profile = token.id AND users_profile_info.status = :status',
        );

        $qb->leftJoin(
            UserProfile::class,
            'users_profile',
            'WITH',
            'users_profile.id = token.id',
        );

        $qb->leftJoin(
            UserProfilePersonal::class,
            'users_profile_personal',
            'WITH',
            'users_profile_personal.event = users_profile.event',
        );


        $qb->setParameter('status', new UserProfileStatus(UserProfileStatusActive::class), UserProfileStatus::TYPE);

        /* Кешируем результат ORM */
        return $qb->enableCache('yandex-market', 86400)->getResult();
    }

    /**
     * Возвращает коллекцию идентификаторов, доступных активному профилю пользователя
     */
    public function getAccessProfileTokenCollection(): ?array
    {
        $UserProfileUid = $this->wbTokenByProfile->getCurrentUserProfile();

        if(!$UserProfileUid)
        {
            return [];
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $select = sprintf('new %s(token.id, users_profile_personal.username)', UserProfileUid::class);
        $qb->select($select);

        $qb->from(YaMarketToken::class, 'token');

        $qb->join(
            YaMarketTokenEvent::class,
            'event',
            'WITH',
            'event.id = token.event',
        );

        $qb->join(
            WbTokenAccess::class,
            'access',
            'WITH',
            'access.event = token.event AND access.profile = :access',
        );


        $qb->setParameter('access', $UserProfileUid, UserProfileUid::TYPE);


        $qb->join(
            UserProfileInfo::class,
            'users_profile_info',
            'WITH',
            'users_profile_info.profile = token.id AND users_profile_info.status = :status',
        );

        $qb->setParameter('status', new UserProfileStatus(UserProfileStatusActive::class), UserProfileStatus::TYPE);

        $qb->leftJoin(
            UserProfile::class,
            'users_profile',
            'WITH',
            'users_profile.id = token.id',
        );

        $qb->leftJoin(
            UserProfilePersonal::class,
            'users_profile_personal',
            'WITH',
            'users_profile_personal.event = users_profile.event',
        );


        /* Кешируем результат ORM */
        return $qb->enableCache('yandex-market', 86400)->getResult();

    }
}