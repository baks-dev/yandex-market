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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEventInterface;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Active\YaMarketTokenActiveDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Business\YaMarketTokenBusinessDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Card\YaMarketTokenCardDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenCompanyDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenExtraDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Percent\YaMarketTokenPercentDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Profile\YaMarketTokenProfileDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Stocks\YaMarketTokenStocksDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Token\YaMarketTokenValueDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Type\YaMarketTokenTypeDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Vat\YaMarketTokenVatDTO;
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
     * Тип (схема работы) токена
     */
    #[Assert\Valid]
    private YaMarketTokenTypeDTO $type;

    /**
     * Профиль пользователя
     */
    #[Assert\Valid]
    private YaMarketTokenProfileDTO $profile;

    /**
     * Токен
     */
    #[Assert\Valid]
    private YaMarketTokenValueDTO $token;

    /**
     * Идентификатор кабинета
     */
    #[Assert\Valid]
    private YaMarketTokenBusinessDTO $business;

    /**
     * Идентификатор компании
     */
    #[Assert\Valid]
    private YaMarketTokenCompanyDTO $company;

    /**
     * Торговая наценка
     */
    #[Assert\Valid]
    private YaMarketTokenPercentDTO $percent;

    /**
     * Статус true = активен / false = заблокирован
     */
    #[Assert\Valid]
    private YaMarketTokenActiveDTO $active;

    /**
     * НДС, применяемый для товара
     */
    #[Assert\Valid]
    private YaMarketTokenVatDTO $vat;

    /**
     * Обновлять карточки токеном
     */
    #[Assert\Valid]
    private YaMarketTokenCardDTO $card;

    /**
     * Запустить продажи
     */
    #[Assert\Valid]
    private YaMarketTokenStocksDTO $stocks;

    public function __construct()
    {
        $this->type = new YaMarketTokenTypeDTO();
        $this->profile = new YaMarketTokenProfileDTO();
        $this->active = new YaMarketTokenActiveDTO();
        $this->business = new YaMarketTokenBusinessDTO();
        $this->company = new YaMarketTokenCompanyDTO();
        $this->percent = new YaMarketTokenPercentDTO();
        $this->token = new YaMarketTokenValueDTO();
        $this->vat = new YaMarketTokenVatDTO();
        $this->card = new YaMarketTokenCardDTO();
        $this->stocks = new YaMarketTokenStocksDTO();

    }

    public function setId(?YaMarketTokenEventUid $id): void
    {
        $this->id = $id;
    }


    public function getEvent(): ?YaMarketTokenEventUid
    {
        return $this->id;
    }

    public function getType(): YaMarketTokenTypeDTO
    {
        return $this->type;
    }

    public function getProfile(): YaMarketTokenProfileDTO
    {
        return $this->profile;
    }

    public function getToken(): YaMarketTokenValueDTO
    {
        return $this->token;
    }

    public function getBusiness(): YaMarketTokenBusinessDTO
    {
        return $this->business;
    }

    public function getCompany(): YaMarketTokenCompanyDTO
    {
        return $this->company;
    }

    public function getPercent(): YaMarketTokenPercentDTO
    {
        return $this->percent;
    }

    public function getActive(): YaMarketTokenActiveDTO
    {
        return $this->active;
    }

    public function getVat(): YaMarketTokenVatDTO
    {
        return $this->vat;
    }

    public function getCard(): YaMarketTokenCardDTO
    {
        return $this->card;
    }

    public function getStocks(): YaMarketTokenStocksDTO
    {
        return $this->stocks;
    }
}
