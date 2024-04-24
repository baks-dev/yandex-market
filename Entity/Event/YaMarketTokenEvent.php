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

namespace BaksDev\Yandex\Market\Entity\Event;


use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Access\WbTokenAccess;
use BaksDev\Yandex\Market\Entity\Cookie\WbTokenCookie;
use BaksDev\Yandex\Market\Entity\Modify\YaMarketTokenModify;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Event */

#[ORM\Entity]
#[ORM\Table(name: 'ya_market_token_event')]
#[ORM\Index(columns: ['profile', 'active'])]
class YaMarketTokenEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: YaMarketTokenEventUid::TYPE)]
    private YaMarketTokenEventUid $id;

    /**
     * ID настройки (профиль пользователя)
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: UserProfileUid::TYPE)]
    private UserProfileUid $profile;

    /**
     * Токен
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private string $token;


    /**
     * Статус true = активен / false = заблокирован
     */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModify::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModify $modify;


    public function __construct()
    {
        $this->id = new YaMarketTokenEventUid();
        $this->modify = new YaMarketTokenModify($this);
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof YaMarketTokenEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof YaMarketTokenEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getId(): YaMarketTokenEventUid
    {
        return $this->id;
    }

    public function getProfile(): UserProfileUid
    {
        return $this->profile;
    }
}