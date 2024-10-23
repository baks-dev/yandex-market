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
use BaksDev\Yandex\Market\UseCase\Admin\Delete\YaMarketTokenDeleteDTO;
use BaksDev\Yandex\Market\UseCase\Admin\Delete\YaMarketTokenDeleteForm;
use BaksDev\Yandex\Market\UseCase\Admin\Delete\YaMarketTokenDeleteHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_YA_MARKET_TOKEN_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/ya/market/token/delete/{id}', name: 'admin.delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity] YaMarketTokenEvent $YaMarketTokenEvent,
        YaMarketTokenDeleteHandler $YaMarketTokenDeleteHandler,
    ): Response
    {

        $YaMarketTokenDeleteDTO = new YaMarketTokenDeleteDTO();
        $YaMarketTokenEvent->getDto($YaMarketTokenDeleteDTO);
        $form = $this->createForm(YaMarketTokenDeleteForm::class, $YaMarketTokenDeleteDTO, [
            'action' => $this->generateUrl('yandex-market:admin.delete', ['id' => $YaMarketTokenDeleteDTO->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('ya_market_token_delete'))
        {
            $this->refreshTokenForm($form);

            $YaMarketToken = $YaMarketTokenDeleteHandler->handle($YaMarketTokenDeleteDTO);

            if($YaMarketToken instanceof YaMarketToken)
            {
                $this->addFlash('breadcrumb.delete', 'success.delete', 'yandex-market.admin');

                return $this->redirectToRoute('yandex-market:admin.index');
            }

            $this->addFlash(
                'breadcrumb.delete',
                'danger.delete',
                'yandex-market.admin',
                $YaMarketToken,
            );

            return $this->redirectToRoute('yandex-market:admin.index', status: 400);
        }

        return $this->render([
            'form' => $form->createView(),
        ]);
    }
}
