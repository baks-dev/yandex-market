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

namespace BaksDev\Yandex\Market\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Yandex\Market\Entity\Event\YaMarketTokenEvent;
use BaksDev\Yandex\Market\Entity\YaMarketToken;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenDTO;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\YaMarketTokenHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_YA_MARKET_TOKEN_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/ya/market/token/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] YaMarketTokenEvent $YaMarketTokenEvent,
        YaMarketTokenHandler $YaMarketTokenHandler,
    ): Response
    {

        $YaMarketTokenDTO = new YaMarketTokenDTO();

        /** Запрещаем редактировать чужой токен */
        if($this->getAdminFilterProfile() === null || $this->getProfileUid()?->equals($YaMarketTokenEvent->getProfile()) === true)
        {
            $YaMarketTokenEvent->getDto($YaMarketTokenDTO);
        }

        if($request->getMethod() === 'GET')
        {
            $YaMarketTokenDTO->hiddenToken();
        }

        // Форма
        $form = $this->createForm(YaMarketTokenForm::class, $YaMarketTokenDTO, [
            'action' => $this->generateUrl(
                'yandex-market:admin.newedit.edit',
                ['id' => $YaMarketTokenDTO->getEvent() ?: new YaMarketTokenEventUid()]
            ),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('ya_market_token'))
        {
            $this->refreshTokenForm($form);

            /** Запрещаем редактировать чужой токен */
            if($this->getAdminFilterProfile() && $this->getAdminFilterProfile()->equals($YaMarketTokenDTO->getProfile()) === false)
            {
                $this->addFlash('breadcrumb.edit', 'danger.edit', 'yandex-market.admin', '404');
                return $this->redirectToReferer();
            }

            $YaMarketToken = $YaMarketTokenHandler->handle($YaMarketTokenDTO);

            if($YaMarketToken instanceof YaMarketToken)
            {
                $this->addFlash('breadcrumb.edit', 'success.edit', 'yandex-market.admin');

                return $this->redirectToRoute('yandex-market:admin.index');
            }

            $this->addFlash('breadcrumb.edit', 'danger.edit', 'yandex-market.admin', $YaMarketToken);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
