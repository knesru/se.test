<?php
/* @var $this HelpController */

$this->setPageTitle('Задания в производство: Помощь');

$this->breadcrumbs = array(
    'Помощь',
);
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/jquery.js');
$cs->registerCssFile($baseUrl . '/css/redmond/jquery-ui.css');
?>
<div class="help-wrapper">
    <h1>ПК Расширение STMS<br/>
        «“Задания в производство”»</h1>
    <div id="toc"><h2>Оглавление</h2><a name="toc"></a></div>
    <h2>Общее описание системы</h2>
    <p>Система предназначена для автоматизации ведения учета заданий для производства</p>
    <h2>Принципы функционирования</h2>
    <h3>Пользовательский интерфейс</h3>
    <p><img style="max-width: 850px" src="/help-images/main_view.PNG"/>
        Главный экран пользователя разделен на три части:
    <ul>
        <li class="goto">Таблица заявок</li>
        <li class="goto">Таблица компонентов</li>
        <li class="goto">Журнал действий пользователя</li>
    </ul>
    Все три области взаимосвязаны.
    </p>
    <h3>Общие принципы</h3>
    <h4>Основные понятия</h4>
    <h5>Из STMS</h5>
    <p>Компонент, имеющий карточку в системе STMS называется «Из STMS»</p>
    <p>Система позволяет работать как с компонентами, имеющими карточку в системе STMS, так и с любыми сторонними</p>
    <p>Компонент, который получил карточку в STMS после заведения в системе “Задания в производство” не считается компонентом «Из STMS»</p>
    <p>При переименовании компонента «Из STMS» в STMS, автоматически производится переименование в производстве. Непосредственно в системе менять наименование таких компонентов нельзя</p>
    <h5>Идентичные компоненты</h5>
    <p>Компоненты считаются идентичными</p>
    <ul>
        <li>Для компонентов «Из STMS» - в случае совпадения <span class="goto" data-ref="Поля таблицы компонентов">ID компонента</span></li>
        <li>Для компонентов не «Из STMS» - в случае совпадения <span class="goto" data-ref="Поля таблицы компонентов">Наименования</span></li>
    </ul>
    <h4>Основные элементы управления</h4>
    <p>Расположение колонок можно менять перетаскиванием заголовков мышью.<img src="/help-images/move_columns.PNG"/></p>
    <p>Видимость настраивается в отдельном <span class="goto" data-ref="Настройки">окне настроек</span> для каждой таблицы отдельно. Чтобы скрыть колонку, требуется
        убрать
        галочку напротив названия колонки в окне настроек.</p>
    <p>Чрезмерно длинный текст в ячейках таблиц сокращается для экономии места и доступен для просмотра при наведении
        мыши и
        при щелчке по строке таблицы.</p>
    <p>Редактирование данных производится непосредственно в таблице. Для этого требуется дважды щелкнуть мышью по
        соответствующей ячейке. Сохранение произойдет автоматически при снятии фокуса выделения с ячейки.</p>
    <p>Большинство действий пользователя логируется и отображается в <span class="goto" data-ref="Журнал действий пользователя">журнале действий.</span></p>
    <h4>Сортировка</h4>
    <p>Сортировка в каждой таблице возможна по любой колонке, содержащей значение.</p>
    <p>Для сортировки требуется щелкнуть мышью по заголовку колонки. Желтый треугольник укажет направление
        сортировки. <img src="/help-images/sorting.PNG"/></p>
    <p>По-умолчанию производится сортировка по приоритету: приоритетные компоненты вверху.</p>
    <h4>Фильтры</h4>
    <p>Фильтрация возможна в таблицах компонентов и заявок.</p>
    <p>Чтобы произвести быстрый поиск по наименованию, требуется ввести часть наименования компонента в поле «быстрый поиск»
        соответствующей таблицы. Поиск произведется по точному совпадению части названия без учета регистра.</p>
    <p>Действие аналогичное фильтрации по колонке «Наименование» с автоматическим применением фильтра после каждого
        введенного
        символа.</p>
    <p>Стандартные поля фильтрации по-умолчанию скрыты для экономии места. Поля можно отобразить, если отметить галочку
        «Фильтр» в заголовке соответствующей таблицы.
    <ul>
        <li>Фильтрация по числовым полям и датам:<br/>
            Если заполнено только первое поле диапазона, будет произведена фильтрация по точному соответствию.<br/>
            Если заполнены оба поля диапазона, будет произведен поиск по диапазону, включающему обе границы.
        </li>
        <li>Фильтрация по текстовым полям:<br/>
            Производится поиск по точному совпадению части текста без учета регистра
        </li>
        <li>Фильтрация по специальным полям<br/>
            Статус и приоритет имеют выпадаюзий список, позволяющий выбрать одно значение.
        </li>
        <li>Галочка «Все заявки» влияет на отображение только таблицы заявок.</li>
        Очистка фильтра производится кнопкой с изображением мусорного бака.
    </ul>
    </p>
    <h4>Экспорт</h4>
    <p>Таблицы заявок и компонентов поддерживают экспорт текущей выборки строчек. В экспортируемый документ попадают все
        строчки, которые содержатся на активной странице таблицы. Для увеличения количества экспортируемых строк можно
        выбрать большее значение элементов на странице в нижней части соответствующей таблицы, однако данная настройка
        может значительно замедлить работу приложения при значениях больших 1000 записей на страницу.</p>
    <p>Экспорт производится в формате xlsx 2007.</p>
    <p>Экспорт может занимать значительное время на формирование до нескольких минут на максимальном(10000) количестве
        записей. В случае, если экспорт производится более 180 секунд(3 минуты), в журнале будет отображено
        соответствующее сообщение.</p>
    <p><i>(i) Автоматическая установка ширины колонок экспортируемого документа не производится по техническим причинам.
        Чтобы произвести автоматическую установку ширины для всего документа, надо выделить все колонки, нажатием на
        левый верхний угол таблицы в Excel или openOffice Calc, и дважды щелкнуть по границе между заголовками
            таблицы.</i></p>
    <h4>История коррекции на складе</h4>
    <p><span class="goto" data-ref="Таблица корректировок склада">Описание таблицы</span></p>
    <p>Для просмотра истории коррекции непосредственно в приложении, требуется выделить строку с компонентом и нажать на кнопку с изображением часов в той же таблице.</p>
    <p>Для компонентов <span class="goto" data-ref="Из STMS">«Из STMS»</span> отображается более полная информация с учетом предусмотренного функционала по корректировке и изъятию, а так же возможности размещения на складе. </p>
    <h4>Постраничный просмотр</h4>
    <p>Нижняя панель таблиц содержит элемент постраничного просмотра.</p>
    <h3>Журнал действий пользователя</h3>
    <p>В журнале действий отображены последние 100 действий, которые произвел пользователь. Каждая запись содержит точное время, описание произведенного действия или результат выполнения.</p>
    <p>Отсутствует возможность автоматически отменять действия.</p>
    <p>Строчки, отмеченные желтым цветом, требуют повышенного внимания.</p>
    <p>Строчки, отмеченные красным цветом содержат сообщения о произошедших серьезных ошибках, как правило, не позволяющих завершить действие при текущих условиях. Большинство ошибок содержат пояснения на русском языке.</p>
    <p>В базе данных хранятся все записанные действия всех пользователей.</p>
    <h3>Настройки</h3>
    <p>Окно настроек</p>
    <p style="text-align: center"><img src="/help-images/settings.PNG" /></p>
    <p>Галочки соответствуют колонкам в таблицах. Чтобы отобразить колонку, требуется отметить соответствующую галочку. Чтобы скрыть - снять выделение.</p>
    <p>Кнопки управления</p>
    <table class="lighttable">
        <tr><td>Сбросить расположение</td><td>Удаляет все сохраненные настройки. Для применения требуется перезагрузка приложения.</td></tr>
        <tr>
            <td>Сохранить расположение</td>
            <td>Сохраняет текущие настройки.
                <ul>
                    <li>Ширина колонок</li>
                    <li><span class="goto" data-ref="Основные элементы управления">Расположение колонок</span></li>
                    <li>Настройки постраничного просмотра</li>
                    <li>Видимость колонок</li>
                </ul>
                Автосохранения нет.
            </td>
        </tr>
        <tr><td>Вернуть пользовательские значения</td><td>Загружает последнюю сохраненную конфигурацию</td></tr>
    </table>
    <h3>Таблица заявок</h3>
    <p>Верхняя таблица</p>
    <p>Таблица содержит список всех компонентов заявок в работе. Таковыми считаются компоненты в любых статусах, кроме «Отмена», «Закрыт». Чтобы отобразить все компоненты, требуется отметить галочку «Все заявки».</p>
    <h4>Поля таблицы</h4>
    См <span class="goto" data-ref="Поля таблицы заявок">Справочники</span>
    <h4>Принять компонент</h4>
    <p>Чтобы принять компонент на склад, требуется выделить соответствующую строку таблицы заявок и нажать кнопку «Принять» в заголовке таблицы.</p>
        <p>Компонент в статусах «Неактивен», «Отмена», «Закрыт» нельзя принять, о чем будет выведено соответствующее предупреждение.</p>
    <p style="text-align: center"><img src="/help-images/receive.PNG" /></p>
        <p>В окне приемки автоматически будет выбрано количество, равное количеству недостающих компонентов.</p>
        <p>Поле «Склад» является обязательным только для компонентов, <span class="goto" data-ref="Из STMS">«Из STMS»</span>.</p>
    <p>Поле <span class="goto" data-ref="Сборщики">«Сборщик»</span> является обязательным.</p>
        <p>Если после приемки количество принятых компонентов станет равным, либо превысит заказанное количество, компонент автоматически перейдет в статус «Закрыт», а чем будет выведено соответствующее предупреждение.</p>
    <p>Если компонент <span class="goto" data-ref="Из STMS">«Из STMS»</span>, будет произведено обновление количества компонента на складе, так же появится запись в <span class="goto" data-ref="Таблица корректировок склада">таблице корректировок</span>.</p>
    <h4>Замена компонента</h4>
    <p>Чтобы заменить компонент другим, требуется выбрать данный компонент в таблице и нажать кнопку «Замена»</p>
    <p>В появившемся окне требуется написать новое значения наименования. В случае, если такое наименование будет найден в STMS, система автоматически предложит его выбрать, в этом случае, компонент будет считаться компонентом <span class="goto" data-ref="Из STMS">«Из STMS»</span></p>
    <p>Если замена успешна</p>
    <ul>
        <li>Будет создана новая строка в таблице заявок, номер строки добавится в примечание к старому компоненту и в лог действий</li>
        <li>У старого компонента будет уменьшено количество заказанных единиц до количества принятых</li>
        <li>У нового компонента количество заказанных единиц станет равным количеству непринятых у старого компонента</li>
    </ul>
    <h3>Таблица компонентов</h3>
    <p>Нижняя таблица</p>
    <h4>Поля таблицы</h4>
    См <span class="goto" data-ref="Поля таблицы компонентов">Справочники</span>
    <p>Таблица содержит список всех компонентов, требующих производства</p>
    <h4>Добавить компонент</h4>
    <h5>Из системы “Задания в производство”</h5>
    <p>Чтобы добавить новый компонент, можно воспользоваться кнопкой на таблице и заполнить соответствующую форму. Поля, отмеченные красной звездочкой обязательны к заполнению</p>
    <p>Новый компонент добавится в нижнюю таблицу со статусом «Неактивен», автоматически заполненными полями «Добавлено» и «Пользователь». Если компонент <span class="goto" data-ref="Из STMS">«Из STMS»</span>, так же автоматически заполнится <span class="goto" data-ref="Поля таблицы компонентов">ID компонента</span></p>
    <h5>Из системы STMS</h5>
    <p>Чтобы добавить новый компонент, можно выбрать его в списке компонентов системы STMS и нажать кнопку «В производство»</p>
    <p>Новый компонент добавится в нижнюю таблицу со статусом <span class="goto" data-ref="Статусы">«Неактивен»</span>, автоматически заполненными полями «Добавлено» и «Пользователь». Так же автоматически заполнится <span class="goto" data-ref="Поля таблицы компонентов">ID компонента</span></p>
    <h4>Создать заявку</h4>
    <p>Чтобы создать заявку из компонента, можно воспользоваться кнопкой «Создать» в колонке <span class="goto" data-ref="Поля таблицы компонентов">«Заявка»</span>, можно использовать кнопку «Создать заявку», предварительно выбрав нужную строку</p>
    <p>Чтобы добавить компонент в существующую заявку, надо выделить строку компонента в нижней таблице и воспользоваться выпадающим меню у кнопки «Создать заявку»</p>
    <h3>Таблица корректировок склада</h3>
    <h4>Поля таблицы</h4>
    См <span class="goto" data-ref="Поля таблицы истории коррекции на складе">Справочники</span>
    <p>Таблица аналогичная по <span class="goto" data-ref="История коррекции на складе">функционалу</span> соответствующей таблице STMS.</p>
    <h3>Сборщики</h3>
    <p>Список сборщиков можно править во вкладке «справочники» в STMS</p>
    <h2>Сценарии</h2>
    <h3>Базовый</h3>
    <ol>
        <li><span class="goto" data-ref="Добавить компонент">Создаем новый компонент</span></li>
        <li><span class="goto" data-ref="Создать заявку">Создаем заявку</span> с этим компонентом</li>
        <li><span class="goto" data-ref="Основные элементы управления">Редактируем</span> <span class="goto" data-ref="Статусы">статус</span> компонента в заявках в зависимости от текущего производственного процесса</li>
        <li><span class="goto" data-ref="Принять компонент">Принимаем компонент</span> на склад</li>
    </ol>
    <img src="/help-images/basic.PNG" />
    <h2>Справочники</h2>
    <h3>Статусы</h3>
    <table class="lighttable">
        <tr><th>Статус</th><th>Номер</th></tr>
        <?php
        $statuses = Extcomponents::getStatuses();
        $statuses_colors = Extcomponents::getStatusesColors();
        foreach ($statuses as $statusid => $status) {
            printf('<tr class="%s"><td>%s</td><td>%d</td></tr>',$statuses_colors[$statusid],$status,$statusid);
            }
        ?>
    </table>
    <h3>Матрица переходов статусов</h3>
    <table class="lighttable">
        <?php
        $statusesMatrix = Extcomponents::getStatusesMatrix();
        $statusesCount = count($statusesMatrix);
        $row = array('');
        for($i=0;$i<$statusesCount;$i++){
            $row[] = $statuses[$i];
        }
        printf('<tr><td>%s</td></tr>',implode('</td><td>',$row));
        $labels = array(
                Extcomponents::C_ALLOW=>'разрешен',
                Extcomponents::C_DENY=>'запрещен',
                Extcomponents::C_SAME=>'',
                Extcomponents::C_AUTO=>'автоматический',
        );
        for($i=0;$i<$statusesCount;$i++){
            $row = array('<td>'.$statuses[$i].'</td>');
            for($j=0;$j<$statusesCount;$j++){
                if(!isset($statusesMatrix[$i][$j])){
                    $statusesMatrix[$i][$j] = 'same';
                }
                $label = sprintf('<span class="hidden-td-label">%s</span>',$labels[$statusesMatrix[$i][$j]]);
                $title = '';
                //«“Задания в производство” ExtComp»
                if(!empty($labels[$statusesMatrix[$i][$j]])) {
                    $title = sprintf('Из состояния «%s» в состояние «%s» переход %s',$statuses[$i],$statuses[$j],$labels[$statusesMatrix[$i][$j]]);
                }
                $row[] = sprintf('<td class="transition-%s" title="%s">%s</td>',$statusesMatrix[$i][$j],$title,$label);
            }
            printf('<tr>%s</tr>',implode('',$row));
        }
        ?>
    </table>
    <h3>Поля таблиц</h3>
    <h4>Поля таблицы заявок</h4>
    <table class="lighttable">
        <tr><td>ID</td><td>Номер строки.</td><td>Натуральное число</td><td>Используется для идентификации внутри приложения и по логу. При перемещении компонента из таблицы компонентов в таблицу заявок сохраняется.</td></tr>
        <tr><td>Заявка</td><td>Номер заявки.</td><td>NN.СБ.ГГ</td><td>Справочное поле. Система не содержит отдельной сущности "заявка". Вся работа ведется с отдельными компонентами.</td></tr>
        <tr><td>Наименование</td><td>Название компонента.</td><td>Текст</td><td>Если компонент взят <span class="goto" data-ref="Из STMS">из STMS</span>, данное поле точно соответствует наименованию. В таком случае, редактировать его нельзя.</td></tr>
        <tr><td>ID компонента</td><td>Идентификатор компонента.</td><td>Натуральное число</td><td>Если компонент взят <span class="goto" data-ref="Из STMS">из STMS</span>, точно соответствует внутреннему идентификационному номеру. В противном случае, поле пустое. По-умолчанию скрыто</td></tr>
        <tr><td>Кол-во</td><td>Запрошенное количество.</td><td>Натуральное число</td><td></td></tr>
        <tr><td>Пользователь</td><td>Пользователь :)</td><td>Список</td><td>Сотрудник, запросивший компонент. Заполняется автоматически.</td></tr>
        <tr><td>Назначение</td><td>Назначение</td><td>Текст</td><td>Текстовое поле.</td></tr>
        <tr><td>Добавлено</td><td>Дата добавления</td><td>Дата</td><td>Заполняется автоматически.</td></tr>
        <tr><td>Сдано</td><td>Количество принятых компонентов</td><td>Целое число</td><td>Заполняется автоматически после приемки</td></tr>
        <tr><td>Скомпл. до</td><td>Скомплектовать до</td><td>Дата</td><td></td></tr>
        <tr><td>Монтаж до</td><td></td><td>Дата</td><td></td></tr>
        <tr><td>Дефицит</td><td></td><td>Текст</td><td></td></tr>
        <tr><td>Примечание</td><td></td><td>Текст</td><td>Заполняется вручную. Дополняется автоматически при замене компонента.</td></tr>
        <tr><td>Монтаж с</td><td></td><td>Дата</td><td>Заполняется вручную.</td></tr>
        <tr><td>Приоритет</td><td></td><td>Логическое</td><td>Два состояния: высокий/низкий приоритет. По-умолчанию, вверху высокий приоритет</td></tr>
        <tr><td><span class="goto" data-ref="Статусы">Статус</span></td><td></td><td>Список</td><td>Текущий статус компонента в заявке</td></tr>
    </table>
    <h4>Поля таблицы компонентов</h4>
    <table class="lighttable">
        <tr><td>ID</td><td>Номер строки.</td><td>Натуральное число</td><td>Используется для идентификации внутри приложения и по логу. При перемещении компонента из таблицы компонентов в таблицу заявок сохраняется.</td></tr>
        <tr><td>Заявка</td><td>Номер заявки.</td><td></td><td>У компонента до перемещения в <span class="goto" data-ref="Таблица заявок">таблицу заявок</span> данное поле пустое. Для удобства, в поле размещена кнопка мгновенной отправки компонента в заявки</td></tr>
        <tr><td>Наименование</td><td>Название компонента.</td><td>Текст</td><td>Если компонент взят <span class="goto" data-ref="Из STMS">из STMS</span>, данное поле точно соответствует наименованию. В таком случае, редактировать его нельзя.</td></tr>
        <tr><td>ID компонента</td><td>Идентификатор компонента.</td><td>Натуральное число</td><td>Если компонент взят <span class="goto" data-ref="Из STMS">из STMS</span>, точно соответствует внутреннему идентификационному номеру. В противном случае, поле пустое. По-умолчанию скрыто</td></tr>
        <tr><td>Кол-во</td><td>Запрошенное количество.</td><td>Натуральное число</td><td></td></tr>
        <tr><td>Пользователь</td><td>Пользователь :)</td><td>Список</td><td>Сотрудник, запросивший компонент. Заполняется автоматически.</td></tr>
        <tr><td>Назначение</td><td>Назначение</td><td>Текст</td><td>Текстовое поле.</td></tr>
        <tr><td>Добавлено</td><td>Дата добавления</td><td>Дата</td><td>Заполняется автоматически.</td></tr>
        <tr><td>Скомпл. до</td><td>Скомплектовать до</td><td>Дата</td><td></td></tr>
        <tr><td>Монтаж до</td><td></td><td>Дата</td><td></td></tr>
        <tr><td>Дефицит</td><td></td><td>Текст</td><td></td></tr>
        <tr><td>Примечание</td><td></td><td>Текст</td><td>Заполняется вручную. Дополняется автоматически при замене компонента.</td></tr>
        <tr><td>Монтаж с</td><td></td><td>Дата</td><td>Заполняется вручную.</td></tr>
        <tr><td>Приоритет</td><td></td><td>Логическое</td><td>Два состояния: высокий/низкий приоритет. По-умолчанию, вверху высокий приоритет</td></tr>
    </table>
    <h4>Поля таблицы истории коррекции на складе</h4>
    <table class="lighttable">
        <tr><td>ID</td><td>Номер строки.</td><td>Натуральное число</td><td>Используется для идентификации внутри приложения</td></tr>
        <tr><td>Пользователь</td><td>Пользователь :)</td><td>Список</td><td>Сотрудник, который произвел действие с компонентом. Заполняется автоматически.</td></tr>
        <tr><td>Добавлено</td><td>Дата добавления</td><td>Дата</td><td>Заполняется автоматически.</td></tr>
        <tr><td>Наименование</td><td>Название компонента.</td><td>Текст</td><td>Если компонент взят <span class="goto" data-ref="Из STMS">из STMS</span>, данное поле точно соответствует наименованию.</td></tr>
        <tr><td>Склад</td><td>Наименование склада</td><td>Список</td><td></td></tr>
        <tr><td>Действие</td><td>Иконка действия</td><td>Список</td><td><span class="ui-icon ui-icon-circle-plus"> </span>Положить<br/><span class="ui-icon ui-icon-circle-minus"> </span>Выдать<br/><span class="ui-icon ui-icon-gear"> </span>Корректировка</td></tr>
        <tr><td>Кол-во</td><td>Количество.</td><td>Целое число</td>
        <td>
                <ul>
                    <li> Для действия "положить" - количество добавленных единиц</li>
                    <li>Для действия "выдать" - количество выданных единиц</li>
                    <li>Для действия "корректировка" - количество единиц на которое произвдена корректировка</li>
                </ul>
            </td></tr>
        <tr><td>До</td><td>Количество до.</td><td>Натуральное число</td><td>Число компонентов на складе до произведения корректировки. Для компонентов не <span class="goto" data-ref="Из STMS">из STMS</span> всегда 0</td></tr>
        <tr><td>После</td><td>Количество после.</td><td>Натуральное число</td><td>Число компонентов на складе после произведения корректировки. Для компонентов не <span class="goto" data-ref="Из STMS">из STMS</span> всегда равно количеству добавляемых единиц</td></tr>
        <tr><td>Примечание</td><td></td><td>Текст</td><td>Заполняется автоматически.<br/> Шаблон - <span class="goto" data-ref="Поля таблицы заявок">Заявка</span>; Сборщик; <span class="goto" data-ref="Поля таблицы компонентов">Примечание компонента</span></td></tr>
    </table>
    <h3>Статусы заказов</h3>
    <table class="lighttable">
        <tr><th>Статус</th><th>Номер</th></tr>
        <?php
        $statuses = Tasks::getStatuses();
        $statuses_colors = Tasks::getStatusesColors();
        foreach ($statuses as $statusid => $status) {
            printf('<tr class="%s"><td>%s</td><td>%d</td></tr>',$statuses_colors[$statusid],$status,$statusid);
        }
        ?>
    </table>
    <h3>Матрица переходов статусов заказов</h3>
    <table class="lighttable">
        <?php
        $statusesMatrix = Tasks::getStatusesMatrix();
        $statusesCount = count($statusesMatrix);
        $row = array('');
        for($i=0;$i<$statusesCount;$i++){
            $row[] = $statuses[$i];
        }
        printf('<tr><td>%s</td></tr>',implode('</td><td>',$row));
        $labels = array(
            Tasks::C_ALLOW=>'разрешен',
            Tasks::C_DENY=>'запрещен',
            Tasks::C_SAME=>'',
            Tasks::C_AUTO=>'автоматический',
        );
        for($i=0;$i<$statusesCount;$i++){
            $row = array('<td>'.$statuses[$i].'</td>');
            for($j=0;$j<$statusesCount;$j++){
                if(!isset($statusesMatrix[$i][$j])){
                    $statusesMatrix[$i][$j] = 'same';
                }
                $label = sprintf('<span class="hidden-td-label">%s</span>',$labels[$statusesMatrix[$i][$j]]);
                $title = '';
                //«“Задания в производство” ExtComp»
                if(!empty($labels[$statusesMatrix[$i][$j]])) {
                    $title = sprintf('Из состояния «%s» в состояние «%s» переход %s',$statuses[$i],$statuses[$j],$labels[$statusesMatrix[$i][$j]]);
                }
                $row[] = sprintf('<td class="transition-%s" title="%s">%s</td>',$statusesMatrix[$i][$j],$title,$label);
            }
            printf('<tr>%s</tr>',implode('',$row));
        }
        ?>
    </table>
</div>
<script type="application/javascript">
    $.fn.tagName = function () {
        return this.prop("tagName");
    };
    $(function () {
        $('body').css({
            'overflow': 'auto',
            'overflow-x': 'hidden'
        });
        let toc = $('#toc');
        let level = 0;
        let current_toc_item = toc;
        let current_item_number = 0;
        let gotoLinks = {};
        $('.goto').each(function () {
            let text = $(this).text();
            if(typeof $(this).data('ref') !== "undefined"){
                text = $(this).data('ref');
            }
            if(typeof gotoLinks[text]==="undefined") {
                gotoLinks[text]=[];
            }
            gotoLinks[text].push($(this));
        });
        $('h1,h2,h3,h4,h5,h6').each(function () {
            let current_level = parseInt($(this).tagName().match(/\d+/)[0]);
            let ancor_name = 'a' + current_item_number;
            $(this).html($('<a class="ah" name="' + ancor_name + '" href="#toc">' + $(this).html() + '</a>'));


            function createTocLink(text,li){
                if(typeof li === "undefined"){
                    li=true;
                }
                if(li) {
                    return $('<li><a class="toc_text" href="#' + ancor_name + '">' + text + '</a></li>');
                }
                return $('<a class="toc_text" href="#' + ancor_name + '">' + text + '</a>');
            }
            let new_toc_item = createTocLink($(this).text());
            if (current_level === level) {
                current_toc_item.parent().append(new_toc_item);
                current_toc_item = new_toc_item;
            }
            if (current_level > level) {
                let new_toc_list = $('<ul class="toc-subitems"></ul>');
                current_toc_item.append(new_toc_list);
                current_toc_item = new_toc_list;
                current_toc_item = current_toc_item.append(new_toc_item);
                current_toc_item = new_toc_item;
            }
            if (current_level < level) {
                let i = current_level;
                while (i < level) {
                    current_toc_item = current_toc_item.parent().parent();
                    i++;
                }
                current_toc_item = current_toc_item.parent().append(new_toc_item);
                current_toc_item = new_toc_item;
            }
            level = current_level;
            current_item_number++;
            if(typeof gotoLinks[$(this).text()] !== "undefined") {
                for(let i=0;i<gotoLinks[$(this).text()].length;i++) {
                    let newGotoLinkItem = createTocLink(gotoLinks[$(this).text()][i].html(), false);
                    gotoLinks[$(this).text()][i].html(newGotoLinkItem);
                }
            }
        });
    });
</script>