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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Messenger\YaMarketTokenMessage;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class YaMarketTokenHandler extends AbstractHandler
{
    //    private EntityManagerInterface $entityManager;
    //
    //    private ValidatorInterface $validator;
    //
    //    private LoggerInterface $logger;
    //
    //    private MessageDispatchInterface $messageDispatch;
    //
    //
    //    public function __construct(
    //        EntityManagerInterface $entityManager,
    //        ValidatorInterface $validator,
    //        LoggerInterface $yandexMarketLogger,
    //        MessageDispatchInterface $messageDispatch,
    //    )
    //    {
    //        $this->entityManager = $entityManager;
    //        $this->validator = $validator;
    //        $this->logger = $yandexMarketLogger;
    //        $this->messageDispatch = $messageDispatch;
    //
    //    }


    /** @see Megamarket */
    public function handle(
        YaMarketTokenDTO $command
    ): string|YaMarketToken {

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new YaMarketToken($command->getProfile());
        $this->event = new YaMarketTokenEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, true) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->entityManager->flush();

        /* Отправляем сообщение в шину */
        $this->messageDispatch->dispatch(
            message: new YaMarketTokenMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'yandex-market'
        );

        return $this->main;
    }


}
