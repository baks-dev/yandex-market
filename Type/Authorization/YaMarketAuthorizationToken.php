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

namespace BaksDev\Yandex\Market\Type\Authorization;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;

final class YaMarketAuthorizationToken
{
    /**
     * ID настройки (профиль пользователя)
     */
    private readonly UserProfileUid $profile;

    /**
     * Токен
     */
    private readonly string $token;

    /**
     * Идентификатор кабинета
     */
    private readonly int $business;

    /**
     * Идентификатор компании
     */
    private int $company;


    /**
     * Торговая наценка
     *
     * Положительное либо отрицательное число в рублях, либо с процентом, пример:
     *
     * 100.1
     * -100.1
     * 10.1%
     * -10.1%
     *
     */
    private string $percent;

    public function __construct(
        UserProfileUid|string $profile,
        string $token,
        int|string $company,
        int|string $business,
        ?string $percent = null
    )
    {

        if(is_string($profile))
        {
            $profile = new UserProfileUid($profile);
        }

        $this->profile = $profile;
        $this->token = $token;
        $this->company = (int) $company;
        $this->business = (int) $business;
        $this->percent = $percent ?: '0';
    }

    public function getProfile(): UserProfileUid
    {
        return $this->profile;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCompany(): int
    {
        return $this->company;
    }

    public function setExtraCompany(int $company): self
    {
        $this->company = $company;
        return $this;
    }


    public function getBusiness(): int
    {
        return $this->business;
    }

    public function getPercent(): string
    {
        return $this->percent;
    }
}
