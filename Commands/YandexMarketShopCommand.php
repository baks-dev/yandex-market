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

namespace BaksDev\Yandex\Market\Commands;

use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\Api\AllShops\YandexMarketShopRequest;
use BaksDev\Yandex\Market\Repository\AllProfileToken\AllProfileYaMarketTokenInterface;
use BaksDev\Yandex\Market\Repository\YaMarketTokensByProfile\YaMarketTokensByProfileInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'baks:yandex:market:shops',
    description: 'Метод получает идентификаторы магазинов',
    aliases: ['baks:yandex:shops']
)]
class YandexMarketShopCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly AllProfileYaMarketTokenInterface $allProfileYaMarketToken,
        private readonly YandexMarketShopRequest $yandexMarketShopRequest,
        private readonly YaMarketTokensByProfileInterface $YaMarketTokensByProfile
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        /** Получаем активные токены авторизации профилей Yandex Market */
        $profiles = $this->allProfileYaMarketToken
            ->onlyActiveToken()
            ->findAll();

        if(false === $profiles || false === $profiles->valid())
        {
            $this->io->warning('Активных профилей пользователя не найден');
            return Command::FAILURE;
        }

        $profiles = iterator_to_array($profiles);


        $helper = $this->getHelper('question');


        /**
         * Интерактивная форма списка профилей
         */

        $questions[] = 'Все';

        foreach($profiles as $quest)
        {
            $questions[] = $quest->getAttr();
        }

        $questions['-'] = 'Выйти';

        $question = new ChoiceQuestion(
            'Профиль пользователя (Ctrl+C чтобы выйти)',
            $questions,
            '0',
        );

        $key = $helper->ask($input, $output, $question);
        $key = $question->getChoices()[$key] ?? false;

        /**
         *  Выходим без выполненного запроса
         */

        if($key === '-' || $key === 'Выйти')
        {
            return Command::SUCCESS;
        }


        /**
         * Выполняем все с возможностью асинхронно в очереди
         */

        if($key === '0' || $key === 'Все')
        {
            /** @var UserProfileUid $profile */
            foreach($profiles as $profile)
            {
                $this->shops($profile);
            }

            return Command::SUCCESS;
        }

        /**
         * Выполняем определенный профиль
         */

        $UserProfileUid = null;

        foreach($profiles as $profile)
        {
            if($profile->getAttr() === $key)
            {
                /* Присваиваем профиль пользователя */
                $UserProfileUid = $profile;
                break;
            }
        }

        if($UserProfileUid)
        {
            $this->shops($UserProfileUid);
            return Command::SUCCESS;
        }

        $this->io->warning('Профиль пользователя не найден');
        return Command::FAILURE;

    }

    public function shops(UserProfileUid $profile)
    {
        $tokensByProfile = $this->YaMarketTokensByProfile->findAll($profile);

        if(false === $tokensByProfile || false === $tokensByProfile->valid())
        {
            $this->io->error('Токенов профиля пользователя не найдено!');
            return;
        }

        foreach($tokensByProfile as $token)
        {
            $shops = $this->yandexMarketShopRequest
                ->forTokenIdentifier($token)
                ->findAll();

            foreach($shops as $shop)
            {
                $this->io->writeln(sprintf('Идентификатор кабинета: %s', $shop->getName()));
                $this->io->writeln(sprintf('Идентификатор кабинета: %s', $shop->getBusiness()));
                $this->io->writeln(sprintf('%s: Идентификатор компании %s', $shop->getType(), $shop->getCompany()));
                $this->io->writeln(PHP_EOL);

            }
        }
    }

}
