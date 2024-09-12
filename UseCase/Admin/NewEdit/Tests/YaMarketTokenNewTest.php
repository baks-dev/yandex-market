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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenExtraDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group yandex-market
 * @group yandex-market-usecase
 */
#[When(env: 'test')]
class YaMarketTokenNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(YaMarketToken::class)
            ->findOneBy(['id' => UserProfileUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }

        $event = $em->getRepository(YaMarketTokenEvent::class)
            ->findBy(['profile' => UserProfileUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {
        /** @see YaMarketTokenDTO */
        $YaMarketTokenDTO = new YaMarketTokenDTO();

        $YaMarketTokenDTO->setToken('yandex_market_token');
        self::assertEquals('yandex_market_token', $YaMarketTokenDTO->getToken());


        $YaMarketTokenDTO->setActive(true);
        self::assertTrue($YaMarketTokenDTO->getActive());

        $UserProfileUid = new UserProfileUid(UserProfileUid::TEST);
        $YaMarketTokenDTO->setProfile($UserProfileUid);
        self::assertSame($UserProfileUid, $YaMarketTokenDTO->getProfile());


        $YaMarketTokenDTO->setBusiness(123456789);
        self::assertEquals(123456789, $YaMarketTokenDTO->getBusiness());


        $YaMarketTokenDTO->setCompany(123456789);
        self::assertEquals(123456789, $YaMarketTokenDTO->getCompany());


        /** Extra Company */

        $YaMarketCompanyDTO = new YaMarketTokenExtraDTO();
        $YaMarketTokenDTO->addExtra($YaMarketCompanyDTO);

        $YaMarketCompanyDTO->setCompany(111111111);
        self::assertEquals(111111111, $YaMarketCompanyDTO->getCompany());


        /** @var YaMarketTokenHandler $YaMarketTokenHandler */
        $YaMarketTokenHandler = self::getContainer()->get(YaMarketTokenHandler::class);
        $handle = $YaMarketTokenHandler->handle($YaMarketTokenDTO);

        self::assertTrue(($handle instanceof YaMarketToken), $handle.': Ошибка YaMarketToken');

    }
}
