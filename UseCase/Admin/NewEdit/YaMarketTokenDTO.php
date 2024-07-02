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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEventInterface;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
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
     * Идентификатор компании
     */
    #[Assert\NotBlank]
    private int $company;

    /**
     * Идентификатор кабинета
     */
    #[Assert\NotBlank]
    private int $business;

    /**
     * Торговая наценка
     */
    #[Assert\NotBlank]
    private int $percent = 0;

    /**
     * Статус true = активен / false = заблокирован
     */
    private bool $active = true;


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

}
