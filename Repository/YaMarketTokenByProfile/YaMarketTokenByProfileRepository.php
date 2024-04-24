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

namespace BaksDev\Yandex\Market\Repository\YaMarketTokenByProfile;


use BaksDev\Auth\Email\Type\EmailStatus\EmailStatus;
use BaksDev\Auth\Email\Type\EmailStatus\Status\EmailStatusActive;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Entity\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Users\User\Type\Id\UserUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class YaMarketTokenByProfileRepository implements YaMarketTokenByProfileInterface
{

    private TokenStorageInterface $tokenStorage;

    private ORMQueryBuilder $ORMQueryBuilder;

    public function __construct(
        ORMQueryBuilder $ORMQueryBuilder,
        TokenStorageInterface $tokenStorage,
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /**
     * Текущий Активный профиль пользователя любой
     */
    public function getCurrentUserProfile(): ?UserProfileUid
    {
        /** @var UserUid $usr */
        $usr = $this->tokenStorage->getToken()
            ?->getUser()
            ?->getId();

        if(!$usr)
        {
            throw new InvalidArgumentException('Невозможно определить авторизованного пользователя');
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $select = sprintf('new %s(profile.id)', UserProfileUid::class);
        $qb->select($select);

        $qb->from(UserProfileInfo::class, 'profile_info');
        $qb->where('profile_info.usr = :user');

        $qb->andWhere('profile_info.active = true');
        $qb->andWhere('profile_info.status = :status');

        $qb->setParameter('user', $usr, UserUid::TYPE);
        $qb->setParameter('status', new EmailStatus(EmailStatusActive::class), EmailStatus::TYPE);

        $qb->join(
            UserProfile::class,
            'profile',
            'WITH',
            'profile.id = profile_info.profile',
        );

        /* Кешируем результат ORM */
        return $qb->enableCache('yandex-market', 86400)->getOneOrNullResult();

    }

    /**
     * Токен авторизации
     */
    public function getToken(UserProfileUid $profile): ?YaMarketAuthorizationToken
    {
        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $select = sprintf('new %s(token.id, event.token)', YaMarketAuthorizationToken::class);
        $qb->select($select);

        $qb
            ->from(YaMarketToken::class, 'token')
            ->where('token.id = :profile')
            ->setParameter('profile', $profile, UserProfileUid::TYPE);

        $qb->join(
            YaMarketTokenEvent::class,
            'event',
            'WITH',
            'event.id = token.event AND event.active = true',
        );

        $qb->join(

            UserProfileInfo::class,
            'info',
            'WITH',
            'info.profile = token.id AND info.status = :status',
        );

        $qb->setParameter('status', new UserProfileStatus(UserProfileStatusActive::class), UserProfileStatus::TYPE);


        /* Кешируем результат ORM */
        return $qb->enableCache('yandex-market', 86400)->getOneOrNullResult();

    }

}