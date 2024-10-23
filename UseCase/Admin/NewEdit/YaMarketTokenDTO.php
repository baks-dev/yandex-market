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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEventInterface;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenExtraDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

/** @see YaMarketTokenEvent */
final class YaMarketTokenDTO implements YaMarketTokenEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?YaMarketTokenEventUid $id = null;

    /**
     * ID настройки (профиль пользователя)
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private ?UserProfileUid $profile = null;

    /**
     * Токен
     */
    private ?string $token = null;


    /**
     * Идентификатор кабинета
     */
    #[Assert\NotBlank]
    private int $business;

    /**
     * Идентификатор компании
     */
    #[Assert\NotBlank]
    private int $company;

    /**
     * Торговая наценка
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 100)]
    private int $percent = 0;

    /**
     * Статус true = активен / false = заблокирован
     */
    private bool $active = true;

    /** Коллекция дополнительных идентификаторов*/
    private ArrayCollection $extra;

    public function __construct()
    {
        $this->extra = new ArrayCollection();
    }


    public function setId(?YaMarketTokenEventUid $id): void
    {
        $this->id = $id;
    }


    public function getEvent(): ?YaMarketTokenEventUid
    {
        return $this->id;
    }


    /**
     * Profile
     */
    public function getProfile(): ?UserProfileUid
    {
        return $this->profile;
    }


    public function setProfile(UserProfileUid $profile): void
    {
        $this->profile = $profile;
    }

    /**
     * Token
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        if(!empty($token))
        {
            $this->token = $token;
        }
    }

    public function hiddenToken(): void
    {
        $this->token = null;
    }


    /**
     * Business
     */
    public function getBusiness(): int
    {
        return $this->business;
    }

    public function setBusiness(int $business): self
    {
        $this->business = $business;
        return $this;
    }

    /**
     * Company
     */
    public function getCompany(): int
    {
        return $this->company;
    }

    public function setCompany(int $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Active
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Percent
     */
    public function getPercent(): int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): self
    {
        $this->percent = $percent;
        return $this;
    }


    /**
     * Company
     */
    public function getExtra(): ArrayCollection
    {
        return $this->extra;
    }

    public function setExtra(ArrayCollection $extra): self
    {
        $this->extra = $extra;
        return $this;
    }

    public function addExtra(YaMarketTokenExtraDTO $company): self
    {
        $filter = $this->extra->filter(function(YaMarketTokenExtraDTO $element) use ($company) {
            return $company->getCompany() === $element->getCompany() && $company->getBusiness() === $element->getBusiness();
        });

        if($filter->isEmpty())
        {
            $this->extra->add($company);
        }

        return $this;
    }

}
