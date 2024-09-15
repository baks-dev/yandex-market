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

namespace BaksDev\Yandex\Market;

use DirectoryIterator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @note: Индекс сортировки 420
 */
class BaksDevYandexMarketBundle extends AbstractBundle
{
    public const NAMESPACE = __NAMESPACE__.'\\';

    public const PATH = __DIR__.DIRECTORY_SEPARATOR;


    //    /**
    //     * @note: Индекс сортировки 420
    //     */
    //    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    //    {
    //        $services = $container->services()
    //            ->defaults()
    //            ->autowire()
    //            ->autoconfigure();
    //
    //        $services->load(self::NAMESPACE, self::PATH)
    //            ->exclude([
    //                self::PATH.'{Entity,Resources,Type}',
    //                self::PATH.'**'.DIRECTORY_SEPARATOR.'*Message.php',
    //                self::PATH.'**'.DIRECTORY_SEPARATOR.'*DTO.php',
    //            ]);
    //    }


    //    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    //    {
    //        $path = self::PATH.implode(DIRECTORY_SEPARATOR, ['Resources', 'config']);
    //
    //        $configs = new \RegexIterator(new \DirectoryIterator($path), '/\.php$/');
    //
    //        foreach($configs as $config)
    //        {
    //            if($config->isDot() || $config->isDir())
    //            {
    //                continue;
    //            }
    //
    //            $container->import($config->getPathname());
    //        }
    //    }
}
