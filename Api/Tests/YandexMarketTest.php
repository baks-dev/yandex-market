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

namespace BaksDev\Yandex\Market\Api\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Api\AllShops\YandexMarketShopDTO;
use BaksDev\Yandex\Market\Api\AllShops\YandexMarketShopRequest;
use BaksDev\Yandex\Market\Type\Authorization\YaMarketAuthorizationToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group yandex-market-test
 */
#[When(env: 'test')]
class YandexMarketTest extends KernelTestCase
{
    private static YaMarketAuthorizationToken $Authorization;

    public static function setUpBeforeClass(): void
    {
        self::$Authorization = new YaMarketAuthorizationToken(
            profile: UserProfileUid::TEST,
            token: $_SERVER['TEST_YANDEX_MARKET_TOKEN'],
            company: (int) $_SERVER['TEST_YANDEX_MARKET_COMPANY'],
            business: (int) $_SERVER['TEST_YANDEX_MARKET_BUSINESS'],
            card: false,
            stocks: false,
        );
    }

    public function testUseCase(): void
    {

        /**
         * FBS
         */

        $AuthorizationFBS = new YaMarketAuthorizationToken(
            profile: UserProfileUid::TEST,
            token: $_SERVER['TEST_YANDEX_MARKET_TOKEN'],
            company: (int) $_SERVER['TEST_YANDEX_MARKET_COMPANY'],
            business: (int) $_SERVER['TEST_YANDEX_MARKET_BUSINESS'],
            card: false,
            stocks: false,
        );



        /** @var YandexMarketShopRequest $YandexMarketShopRequest */
        $YandexMarketShopRequest = self::getContainer()->get(YandexMarketShopRequest::class);
        $YandexMarketShopRequest->TokenHttpClient($AuthorizationFBS);

        $shops = $YandexMarketShopRequest->findAll();

        self::assertTrue($shops->valid());
        self::assertInstanceOf(YandexMarketShopDTO::class, $shops->current());

        /** @var YandexMarketShopDTO $YandexMarketShopDTO */

        foreach($shops as $YandexMarketShopDTO)
        {
            self::assertInstanceOf(UserProfileUid::class, $YandexMarketShopDTO->getProfile());
            self::assertIsInt($YandexMarketShopDTO->getCompany());
            self::assertIsInt($YandexMarketShopDTO->getClient());
            self::assertIsInt($YandexMarketShopDTO->getBusiness());
            self::assertIsString($YandexMarketShopDTO->getName());
            self::assertIsString($YandexMarketShopDTO->getType());
        }


        /**
         * DBS
         */

        $AuthorizationDBS = new YaMarketAuthorizationToken(
            profile: UserProfileUid::TEST,
            token: $_SERVER['TEST_YANDEX_MARKET_TOKEN'],
            company: (int) $_SERVER['TEST_YANDEX_MARKET_COMPANY'],
            business: (int) $_SERVER['TEST_YANDEX_MARKET_BUSINESS'],
            card: false,
            stocks: false,
        );


        /** @var YandexMarketShopRequest $YandexMarketShopRequest */
        $YandexMarketShopRequest = self::getContainer()->get(YandexMarketShopRequest::class);
        $YandexMarketShopRequest->TokenHttpClient($AuthorizationDBS);

        $shops = $YandexMarketShopRequest->findAll();

        self::assertTrue($shops->valid());
        self::assertInstanceOf(YandexMarketShopDTO::class, $shops->current());

        /** @var YandexMarketShopDTO $YandexMarketShopDTO */
        foreach($shops as $YandexMarketShopDTO)
        {
            self::assertInstanceOf(UserProfileUid::class, $YandexMarketShopDTO->getProfile());
            self::assertIsInt($YandexMarketShopDTO->getCompany());
            self::assertIsInt($YandexMarketShopDTO->getClient());
            self::assertIsInt($YandexMarketShopDTO->getBusiness());
            self::assertIsString($YandexMarketShopDTO->getName());
            self::assertIsString($YandexMarketShopDTO->getType());
        }

    }

}
