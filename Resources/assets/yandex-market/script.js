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


/** Добавить контактный телефон */

document.querySelectorAll('#add_company').forEach(function(item)
{
    item.addEventListener('click', addTokenCompany);
});

function addTokenCompany()
{
    /* Получаем прототип формы */
    let newForm = this.dataset.prototype;
    let index = this.dataset.index * 1;
    let collection = this.dataset.collection;

    newForm = newForm.replace(/__company__/g, index);

    let div = document.createElement('div');
    div.innerHTML = newForm;

    let delButton = div.querySelector('.del-item-company');

    if(delButton === null)
    {
        return;
    }

    div.id = delButton.dataset.delete;
    div.classList.add('mb-3');

    let $collection = document.getElementById(collection);
    $collection.append(div);


    /* Удаляем контактный номер телефона */
    delButton.addEventListener('click', deleteTokenCompany);

    this.dataset.index = (index + 1).toString();
}

document.querySelectorAll('.del-item-company').forEach(function(item)
{
    item.addEventListener('click', deleteTokenCompany);
});

function deleteTokenCompany()
{
    document.getElementById(this.dataset.delete).remove();
}