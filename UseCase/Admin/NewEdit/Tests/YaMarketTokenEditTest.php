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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Tests;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Repository\YaMarketTokenCurrentEvent\YaMarketTokenCurrentEventInterface;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenExtraDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenHandler;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('yandex-market')]
class YaMarketTokenEditTest extends KernelTestCase
{
    #[DependsOnClass(YaMarketTokenNewTest::class)]
    public function testUseCase(): void
    {
        /** @var YaMarketTokenCurrentEventInterface $YaMarketTokenCurrentEvent */
        $YaMarketTokenCurrentEvent = self::getContainer()->get(YaMarketTokenCurrentEventInterface::class);

        $YaMarketTokenEvent = $YaMarketTokenCurrentEvent
            ->forMain(new YaMarketTokenUid(YaMarketTokenUid::TEST))
            ->find();

        self::assertNotFalse($YaMarketTokenEvent);
        self::assertNotNull($YaMarketTokenEvent);

        /** @see YaMarketTokenDTO */
        $YaMarketTokenDTO = new YaMarketTokenDTO();
        $YaMarketTokenEvent->getDto($YaMarketTokenDTO);


        self::assertEquals('yandex_market_token', $YaMarketTokenDTO->getToken());
        $YaMarketTokenDTO->setToken('yandex_market_token_edit');


        self::assertTrue($YaMarketTokenDTO->getActive());
        $YaMarketTokenDTO->setActive(false);


        self::assertTrue($YaMarketTokenDTO->getProfile()->equals(UserProfileUid::TEST)); //($UserProfileUid::TEST, $YaMarketTokenDTO->getProfile());
        $UserProfileUid = new UserProfileUid(UserProfileUid::TEST);
        $YaMarketTokenDTO->setProfile(clone $UserProfileUid);


        self::assertEquals(123456789, $YaMarketTokenDTO->getBusiness());
        $YaMarketTokenDTO->setBusiness(987654321);


        self::assertEquals(123456789, $YaMarketTokenDTO->getCompany());
        $YaMarketTokenDTO->setCompany(987654321);


        /** Extra Company */


        /** @var YaMarketTokenExtraDTO $YaMarketCompanyDTO */
        $YaMarketCompanyDTO = $YaMarketTokenDTO->getExtra()->current();

        self::assertEquals(111111111, $YaMarketCompanyDTO->getCompany());
        $YaMarketCompanyDTO->setCompany(222222222);


        /** @var YaMarketTokenHandler $YaMarketTokenHandler */
        $YaMarketTokenHandler = self::getContainer()->get(YaMarketTokenHandler::class);
        $handle = $YaMarketTokenHandler->handle($YaMarketTokenDTO);

        self::assertTrue(($handle instanceof YaMarketToken), $handle.': Ошибка YaMarketToken');

    }
}
