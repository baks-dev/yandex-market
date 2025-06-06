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

namespace BaksDev\Yandex\Market\UseCase\Admin\NewEdit;

use BaksDev\Users\Profile\UserProfile\Repository\UserProfileChoice\UserProfileChoiceInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Active\YaMarketTokenActiveForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Business\YaMarketTokenBusinessForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Card\YaMarketTokenCardForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Company\YaMarketTokenCompanyForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Percent\YaMarketTokenPercentForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Profile\YaMarketTokenProfileFrom;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Token\YaMarketTokenValueForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Type\YaMarketTokenTypeForm;
use BaksDev\Yandex\Market\UseCase\Admin\NewEdit\Vat\YaMarketTokenVatForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class YaMarketTokenForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('active', YaMarketTokenActiveForm::class, ['label' => false]);

        $builder->add('business', YaMarketTokenBusinessForm::class, ['label' => false]);

        $builder->add('company', YaMarketTokenCompanyForm::class, ['label' => false]);

        $builder->add('percent', YaMarketTokenPercentForm::class, ['label' => false]);

        $builder->add('profile', YaMarketTokenProfileFrom::class, ['label' => false]);

        $builder->add('token', YaMarketTokenValueForm::class, ['label' => false]);

        $builder->add('type', YaMarketTokenTypeForm::class, ['label' => false]);

        $builder->add('vat', YaMarketTokenVatForm::class, ['label' => false]);

        $builder->add('card', YaMarketTokenCardForm::class, ['label' => false]);


        //        /* Коллекция продукции */
        //        $builder->add('extra', CollectionType::class, [
        //            'entry_type' => Company\YaMarketCompanyForm::class,
        //            'entry_options' => ['label' => false],
        //            'label' => false,
        //            'by_reference' => false,
        //            'allow_delete' => true,
        //            'allow_add' => true,
        //            'prototype_name' => '__company__',
        //        ]);


        /* Сохранить ******************************************************/
        $builder->add(
            'ya_market_token',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']]
        );
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => YaMarketTokenDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
