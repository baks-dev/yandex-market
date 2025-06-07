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

final  class YaMarketAuthorizationToken
{
    public function __construct(
        private readonly string $profile,
        private readonly string $token,
        private readonly int|string $company,
        private readonly int|string $business,

        private readonly ?bool $card,
        private readonly ?bool $stocks,

        private ?string $percent = null,
        private ?string $vat = null
    ) {}

    /**
     * ID настройки (профиль пользователя)
     */
    public function getProfile(): UserProfileUid
    {
        return new UserProfileUid($this->profile);
    }

    /**
     * Токен
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Идентификатор компании
     */
    public function getCompany(): int
    {
        return $this->company;
    }


    /**
     * Идентификатор кабинета
     */
    public function getBusiness(): int
    {
        return $this->business;
    }

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
    public function getPercent(): string
    {
        return $this->percent ?: '0';
    }

    public function isCard(): bool
    {
        return $this->card === true;
    }

    public function isStocks(): bool
    {
        return $this->stocks === true;
    }

    /**
     * Если параметр не указан, используется НДС, установленный в кабинете.
     */
    public function getVat(): string|false
    {
        if(is_null($this->vat))
        {
            return false;
        }

        return match (true)
        {
            $this->vat === 0 => 5, // 5 — НДС 0%. Например, используется при продаже товаров, вывезенных в таможенной процедуре экспорта, или при оказании услуг по международной перевозке товаров.
            $this->vat === 5 => 10, // 10 — НДС 5%. НДС для упрощенной системы налогообложения (УСН).
            $this->vat === 7 => 11, // 11 — НДС 7%. НДС для упрощенной системы налогообложения (УСН).
            $this->vat === 10 => 2, // 2 — НДС 10%. Например, используется при реализации отдельных продовольственных и медицинских товаров.
            $this->vat === 20 => 7, // 7 — НДС 20%. Основной НДС с 2019 года.
            default => false,
        };
    }
}
