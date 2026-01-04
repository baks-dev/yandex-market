<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Yandex\Market\UseCase\Admin\Delete\Tests;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Repository\YaMarketTokenCurrentEvent\YaMarketTokenCurrentEventInterface;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use BaksDev\Yandex\Market\UseCase\Admin\Delete\YaMarketTokenDeleteDTO;
use BaksDev\Yandex\Market\UseCase\Admin\Delete\YaMarketTokenDeleteHandler;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenExtraDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Tests\YaMarketTokenEditTest;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Tests\YaMarketTokenNewTest;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('yandex-market')]
class YaMarketTokenDeleteTest extends KernelTestCase
{
    #[DependsOnClass(YaMarketTokenNewTest::class)]
    #[DependsOnClass(YaMarketTokenEditTest::class)]
    public function testUseCase(): void
    {
        /** @var YaMarketTokenCurrentEventInterface $YaMarketTokenCurrentEvent */
        $YaMarketTokenCurrentEvent = self::getContainer()->get(YaMarketTokenCurrentEventInterface::class);

        $YaMarketTokenEvent = $YaMarketTokenCurrentEvent
            ->forMain(new YaMarketTokenUid(YaMarketTokenUid::TEST))
            ->find();

        self::assertNotNull($YaMarketTokenEvent);
        self::assertNotFalse($YaMarketTokenEvent);


        /** @see YaMarketTokenDTO */
        $YaMarketTokenDTO = new YaMarketTokenDTO();
        $YaMarketTokenEvent->getDto($YaMarketTokenDTO);

        self::assertEquals('yandex_market_token_edit', $YaMarketTokenDTO->getToken()->getValue());
        self::assertFalse($YaMarketTokenDTO->getActive()->getValue());
        self::assertFalse($YaMarketTokenDTO->getProfile()->getValue()->equals(UserProfileUid::TEST));
        self::assertEquals(987654321, $YaMarketTokenDTO->getBusiness()->getValue());
        self::assertEquals(987654321, $YaMarketTokenDTO->getCompany()->getValue());


        /** Extra Company */

        //        /** @var YaMarketTokenExtraDTO $YaMarketCompanyDTO */
        //        $YaMarketCompanyDTO = $YaMarketTokenDTO->getExtra()->current();
        //        self::assertEquals(222222222, $YaMarketCompanyDTO->getCompany());


        /** @see YaMarketTokenDeleteDTO */
        $YaMarketTokenDeleteDTO = new YaMarketTokenDeleteDTO();
        $YaMarketTokenEvent->getDto($YaMarketTokenDeleteDTO);

        /** @var YaMarketTokenDeleteHandler $YaMarketTokenHandler */
        $YaMarketTokenDeleteHandler = self::getContainer()->get(YaMarketTokenDeleteHandler::class);
        $handle = $YaMarketTokenDeleteHandler->handle($YaMarketTokenDeleteDTO);

        self::assertTrue(($handle instanceof YaMarketToken), $handle.': Ошибка YaMarketToken');

    }

    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(YaMarketToken::class)
            ->find(YaMarketTokenUid::TEST);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(YaMarketTokenEvent::class)
            ->findBy(['main' => YaMarketTokenUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }
}
