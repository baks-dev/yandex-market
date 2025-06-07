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

namespace BaksDev\Yandex\Market\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Core\Type\Ip\IpAddress;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\Active\YaMarketTokenActive;
use BaksDev\Yandex\Market\Entity\Event\Business\YaMarketTokenBusiness;
use BaksDev\Yandex\Market\Entity\Event\Card\YaMarketTokenCard;
use BaksDev\Yandex\Market\Entity\Event\Company\YaMarketTokenCompany;
use BaksDev\Yandex\Market\Entity\Event\Modify\Action\YaMarketTokenModifyAction;
use BaksDev\Yandex\Market\Entity\Event\Modify\DateTime\YaMarketTokenModifyDateTime;
use BaksDev\Yandex\Market\Entity\Event\Modify\IpAddress\YaMarketTokenModifyIpAddress;
use BaksDev\Yandex\Market\Entity\Event\Modify\User\YaMarketTokenModifyUser;
use BaksDev\Yandex\Market\Entity\Event\Modify\UserAgent\YaMarketTokenModifyUserAgent;
use BaksDev\Yandex\Market\Entity\Event\Percent\YaMarketTokenPercent;
use BaksDev\Yandex\Market\Entity\Event\Profile\YaMarketTokenProfile;
use BaksDev\Yandex\Market\Entity\Event\Stocks\YaMarketTokenStocks;
use BaksDev\Yandex\Market\Entity\Event\Token\YaMarketTokenValue;
use BaksDev\Yandex\Market\Entity\Event\Type\YaMarketTokenType;
use BaksDev\Yandex\Market\Entity\Event\Vat\YaMarketTokenVat;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Event */

#[ORM\Entity]
#[ORM\Table(name: 'ya_market_token_event')]
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
    #[ORM\Column(type: YaMarketTokenUid::TYPE)]
    private YaMarketTokenUid $main;


    /**
     * ID настройки (профиль пользователя)
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenProfile::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenProfile $profile = null;


    /**
     * Тип (схема работы) токена
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenType::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenType $type = null;

    /**
     * Токен
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenValue::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenValue $token = null;


    /**
     * Статус true = активен / false = заблокирован
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenActive::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenActive $active = null;


    /**
     * Идентификатор компании
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenCompany::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenCompany $company = null;


    /**
     * Идентификатор кабинета
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenBusiness::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenBusiness $business = null;

    /**
     * Торговая наценка
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenPercent::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenPercent $percent = null;

    /**
     * НДС, применяемый для товара
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenVat::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenVat $vat = null;


    /**
     * Обновлять карточки токеном
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenCard::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenCard $card = null;

    /**
     * Запустить продажи
     */
    #[ORM\OneToOne(targetEntity: YaMarketTokenStocks::class, mappedBy: 'event', cascade: ['all'])]
    private ?YaMarketTokenStocks $stocks = null;


    /**
     * Модификатор
     */

    /** YaMarketTokenModifyAction */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModifyAction::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModifyAction $action;

    /** YaMarketTokenModifyDateTime */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModifyDateTime::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModifyDateTime $datetime;

    /** YaMarketTokenModifyUserAgent */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModifyUserAgent::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModifyUserAgent $agent;


    /** YaMarketTokenModifyIpAddress */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModifyIpAddress::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModifyIpAddress $ipv;

    /** YaMarketTokenModifyUser */
    #[ORM\OneToOne(targetEntity: YaMarketTokenModifyUser::class, mappedBy: 'event', cascade: ['all'])]
    private YaMarketTokenModifyUser $user;

    public function __construct()
    {
        $this->id = new YaMarketTokenEventUid();

        $this->action = new YaMarketTokenModifyAction($this);
        $this->datetime = new YaMarketTokenModifyDateTime($this);
        $this->agent = new YaMarketTokenModifyUserAgent($this);
        $this->user = new YaMarketTokenModifyUser($this);
        $this->ipv = new YaMarketTokenModifyIpAddress($this);
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function setMain(YaMarketToken|YaMarketTokenUid $main): self
    {
        $this->main = $main instanceof YaMarketToken ? $main->getId() : $main;

        return $this;
    }

    public function getId(): YaMarketTokenEventUid
    {
        return $this->id;
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

    public function equalsTokenProfile(UserProfileUid $profile): bool
    {
        return $this->profile->getValue()->equals($profile);
    }

    public function getAction(): YaMarketTokenModifyAction
    {
        return $this->action;
    }

    public function getAgent(): YaMarketTokenModifyUserAgent
    {
        return $this->agent;
    }

    public function getIpAddress(): YaMarketTokenModifyIpAddress
    {
        return $this->ipv;
    }

    public function getUser(): YaMarketTokenModifyUser
    {
        return $this->user;
    }





    //    public function setAgent(string $agent): self
    //    {
    //        $this->agent->setValue($agent);
    //        return $this;
    //    }
    //
    //    public function setUser(YaMarketTokenModifyUser $user): self
    //    {
    //        $this->user->setValue($user);
    //        return $this;
    //    }
    //
    //    public function setIpAddress(?YaMarketTokenModifyIpAddress $IpAddress): self
    //    {
    //        $this->ipv = $IpAddress;
    //        return $this;
    //    }
}
