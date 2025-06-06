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

namespace BaksDev\Yandex\Market\Type\Id;

use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;


final class YaMarketTokenUid extends Uid
{

    public const string TEST = 'dc7220a5-192a-7f88-bb3f-cdb3424e45d8';

    public const string TYPE = 'ya_market_token';

    public function __construct(
        AbstractUid|string|null $value = null,
        private ?string $option = null,
    )
    {
        parent::__construct($value);
    }

    /**
     * @throws \JsonException
     */
    public function getOption(): ?array
    {
        if(empty($this->option))
        {
            return null;
        }

        if(false === json_validate($this->option, 512, \JSON_THROW_ON_ERROR))
        {
            return null;
        }

        $option = json_decode($this->option, true, 512, \JSON_THROW_ON_ERROR);

        return empty($option) ? null : $option;
    }
}