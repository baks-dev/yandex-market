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

namespace BaksDev\Yandex\Market\Api\FBY\AllShops\Tests;

use BaksDev\Orders\Order\Type\Id\OrderUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Wildberries\Api\Token\Card\WildberriesCards\Card;
use BaksDev\Wildberries\Api\Token\Card\WildberriesCards\WildberriesCards;
use BaksDev\Wildberries\Api\Token\Orders\WildberriesOrdersSticker\WildberriesOrdersSticker;
use BaksDev\Wildberries\Api\Token\Stocks\GetStocks\WildberriesStocks;
use BaksDev\Wildberries\Api\Token\Supplies\SupplyInfo\WildberriesSupplyInfo;

//use BaksDev\Wildberries\Api\Token\Warehouse\WarehousesWildberries\SellerWarehouses;
//use BaksDev\Wildberries\Api\Token\Warehouse\WarehousesWildberries\SellerWarehouse;
use BaksDev\Wildberries\Orders\Entity\Event\WbOrdersEvent;
use BaksDev\Wildberries\Orders\Entity\WbOrders;
use BaksDev\Wildberries\Package\Type\Supply\Status\WbSupplyStatus\Collection\WbSupplyStatusCollection;
use BaksDev\Wildberries\Type\Authorization\WbAuthorizationToken;
use BaksDev\Yandex\Market\Api\FBY\AllShops\YandexMarketShopRequest;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group yandex-market
 * @group yandex-market-api
 */
#[When(env: 'test')]
final class YandexMarketShopTest extends KernelTestCase
{
    private static ?string $tocken = null;

    public static function setUpBeforeClass(): void
    {
        if(isset($_SERVER['TEST_YANDEX_MARKET_CLIENT']))
        {
            self::$tocken = $_SERVER['TEST_YANDEX_MARKET_CLIENT'];
        }
    }

    public function testUseCase(): void
    {
        self::assertNotNull(self::$tocken);

        if(self::$tocken)
        {


            /** @var YandexMarketShopRequest $YandexMarketShopRequest */
            $YandexMarketShopRequest = self::getContainer()->get(YandexMarketShopRequest::class);

            $YandexMarketShopRequest->TokenHttpClient(new YaMarketAuthorizationToken(new UserProfileUid(), self::$tocken));

        }

    }
}