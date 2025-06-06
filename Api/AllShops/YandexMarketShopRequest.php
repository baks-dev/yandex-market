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

namespace BaksDev\Yandex\Market\Api\AllShops;

use BaksDev\Yandex\Market\Api\YandexMarket;
use DateInterval;
use DomainException;
use Generator;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

final class YandexMarketShopRequest extends YandexMarket
{
    /**
     * Возвращает список магазинов, к которым имеет доступ пользователь — владелец авторизационного токена,
     * использованного в запросе. Для агентских пользователей список состоит из подагентских магазинов.
     *
     * ОГРАНИЧЕНИЕ! 1000 запросов в час
     *
     * @see https://yandex.ru/dev/market/partner-api/doc/ru/reference/campaigns/getCampaigns
     *
     * @return \Generator<int, YandexMarketShopDTO>|false
     *
     */
    public function findAll(): Generator|false
    {
        /** Кешируем результат запроса */

        $cache = $this->getCacheInit('yandex-market'); // new FilesystemAdapter('yandex-market');
        $key = 'ya-market-shops-'.$this->getProfile();

        $content = $cache->get($key, function(ItemInterface $item) {

            $item->expiresAfter(DateInterval::createFromDateString('1 seconds'));

            $response = $this->TokenHttpClient()
                ->request(
                    'GET',
                    '/campaigns',
                );

            $content = $response->toArray(false);

            if($response->getStatusCode() !== 200)
            {
                foreach($content['errors'] as $error)
                {
                    $this->logger->critical($error['code'].': '.$error['message'], [self::class.':'.__LINE__]);
                }

                return false;
            }


            $item->expiresAfter(DateInterval::createFromDateString('1 day'));

            return $response->toArray(false);

        });

        if(empty($content) || false === isset($content['campaigns']))
        {
            return false;
        }

        foreach($content['campaigns'] as $data)
        {
            yield new YandexMarketShopDTO($this->getProfile(), $data);
        }
    }
}
