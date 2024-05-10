<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Yandex\Market\Controller\Admin\Tests;

use BaksDev\Users\User\Tests\TestUserAccount;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/** @group yandex-market */
#[When(env: 'test')]
final class DeleteControllerTest extends WebTestCase
{
    private const URL = '/admin/ya/market/token/delete/%s';

    private const ROLE = 'ROLE_YA_MARKET_TOKEN_DELETE';

    private static ?YaMarketTokenEventUid $identifier = null;

    public static function setUpBeforeClass(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        self::$identifier = $em->getRepository(YaMarketToken::class)->findOneBy([], ['id' => 'DESC'])?->getEvent();

        $em->clear();
        //$em->close();
    }


    /** Доступ по роли */
    public function testRoleSuccessful(): void
    {
        // Получаем одно из событий
        $Event = self::$identifier;

        if($Event)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach(TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $usr = TestUserAccount::getModer(self::ROLE);

                $client->loginUser($usr, 'user');
                $client->request('GET', sprintf(self::URL, $Event->getValue()));

                self::assertResponseIsSuccessful();
            }
        }

        self::assertTrue(true);
    }

    // доступ по роли ROLE_ADMIN
    public function testRoleAdminSuccessful(): void
    {
        // Получаем одно из событий
        $Event = self::$identifier;

        if($Event)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach(TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $usr = TestUserAccount::getAdmin();

                $client->loginUser($usr, 'user');
                $client->request('GET', sprintf(self::URL, $Event->getValue()));

                self::assertResponseIsSuccessful();
            }
        }

        self::assertTrue(true);
    }

    // доступ по роли ROLE_USER
    public function testRoleUserDeny(): void
    {
        // Получаем одно из событий
        $Event = self::$identifier;

        if($Event)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach(TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $usr = TestUserAccount::getUsr();
                $client->loginUser($usr, 'user');
                $client->request('GET', sprintf(self::URL, $Event->getValue()));

                self::assertResponseStatusCodeSame(403);
            }
        }

        self::assertTrue(true);
    }

    /** Доступ по без роли */
    public function testGuestFiled(): void
    {
        // Получаем одно из событий
        $Event = self::$identifier;

        if($Event)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach(TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $client->request('GET', sprintf(self::URL, $Event->getValue()));

                // Full authentication is required to access this resource
                self::assertResponseStatusCodeSame(401);
            }
        }

        self::assertTrue(true);
    }
}
