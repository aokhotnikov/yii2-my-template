<?php
namespace app\components;

use Yii;

class Helpers
{
    static function ukr2translit($str)
    {
        $ukr = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Є',  'Ж',  'З', 'И', 'І', 'Ї',  'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х',  'Ц',  'Ч',  'Ш',  'Щ',    'Ь', 'Ю',  'Я',
                'а', 'б', 'в', 'г', 'д', 'е', 'є',  'ж',  'з', 'и', 'і', 'ї',  'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',  'ц',  'ч',  'ш',  'щ',    'ь', 'ю',  'я', '\''];
        $lat = ['A', 'B', 'V', 'G', 'D', 'E', 'Ye', 'Zh', 'Z', 'Y', 'I', 'Yi', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Kh', 'Ts', 'Ch', 'Sh', 'Shch', '',  'Yu', 'Ya',
                'a', 'b', 'v', 'g', 'd', 'e', 'ie', 'zh', 'z', 'y', 'i', 'i',  'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'ts', 'ch', 'sh', 'shch', '',  'iu', 'ia', ''];

        return str_replace($ukr, $lat, $str);
    }

    static function showNotify($message, $type = 'success', $customMessage = false, $icon = 'glyphicon-ok')
    {
        if (!$customMessage) {

            Yii::$app->session->set('message', [
                'type' => $type,
                'message' => $message,
            ]);
        } else {
            // Создание уведомления с расширенными параметрами
            Yii::$app->session->set('message', [
                'type' => $type,                           // класс сообщения (success, info, warning, danger)
                'icon' => 'glyphicon ' . $icon,            // картинка перед сообщением, тип смотрим ниже
                'icon_type' => 'class',                    // тип иконки в данном случае это класс bootstrap иконки,
                // для картинки image, а в icon указываем путь до картинки
                //'title' => '<strong style="margin-left: 10px;">Спасибо</strong><br>',      // заголовок
                'message' => $message,                     // текст сообщения

                'element' => 'body',                       // к какому элементу применяется уведомление
                'position' => 'absolute',                  // позиция контейнера элемента (static | fixed | relative | absolute)
                'allow_dismiss' => '0',                    // позволять пользователю закрывать уведомление (1 - да, 0 - нет)
                'newest_on_top' => '0',                    // новое уведомление заменяет старое (1 - да, 0 - нет)
                //'showProgressbar' => '1',                  // показывать прогресс бар (1 - да, 0 - нет)
                //'url' => 'http://phpnt.com/',              // ссылка при клике на уведомление
                'target' => '_blank',                      // target ссылки

                'placement_from' => 'top',                 // позиция по вертикали (top или bottom)
                'placement_align' => 'center',             // позиция по горизонтали (left, center или right)

                'offset' => 20,                            // смещение от свойства placement_align (если left - смещение от левого края)
                'offset_x' => 20,                          // растояние между элементами уведомлений по оси x в писелях
                'offset_y' => 20,                          // растояние между элементами уведомлений по оси y в писелях
                'spacing' => 20,                           // расстояние между блоками
                'z_index' => 1031,                         // z-index
                'delay' => 5000,                           // время показа уведомления

                'animate_enter' => 'animated fadeIn',      // анимация для начала показа
                'animate_exit' => 'animated fadeOut',      // анимация для конца показа
                'template' => '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button><span data-notify="icon"></span><span data-notify="title">{1}</span><span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>',
                // шаблон сообщения, здесь {0} = type, {1} = title, {2} = message, {3} = url, {4} = target
            ]);
        }
    }
}