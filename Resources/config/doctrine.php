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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Yandex\Market\BaksDevYandexMarketBundle;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventType;
use BaksDev\Yandex\Market\Type\Event\YaMarketTokenEventUid;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenType;
use BaksDev\Yandex\Market\Type\Id\YaMarketTokenUid;
use Symfony\Config\DoctrineConfig;

return static function(DoctrineConfig $doctrine, ContainerConfigurator $configurator): void {

    $doctrine->dbal()->type(YaMarketTokenUid::TYPE)->class(YaMarketTokenType::class);
    $doctrine->dbal()->type(YaMarketTokenEventUid::TYPE)->class(YaMarketTokenEventType::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault
        ->mapping('yandex-market')
        ->type('attribute')
        ->dir(BaksDevYandexMarketBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevYandexMarketBundle::NAMESPACE.'\\Entity')
        ->alias('yandex-market');
};

