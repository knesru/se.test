<?php
/**
 * @var Tasks $tasks_model
 * @var Products $products_model
 * @var UActiveForm $form
 */
?>
<div class="trashcan"></div>
<p class="note">Поля, отмеченные <span class="required">*</span>, обязательны для заполнения.</p>
<style>
    .acceptor-block{
        display: inline-block;
        margin: 10px 10px 0 0;
        border: 1px solid #eee;
        padding: 5px 0 0 5px;
        vertical-align: top;
    }
    .packages-list {
        width: 295px;
        min-height: 20px;
        list-style-type: none;
        padding: 5px 0 0 0;
    }

    .packages-list li {
        margin: 0 5px 5px 5px;
        padding: 5px;
        font-size: 1.2em;
        width: 290px;
    }

    .packages-list li input[type="text"] {
        width: 284px;
    }

    .packages-list li input.mini-text-fld {
        width: 120px;
        padding-right: 5px;
    }

    .acceptors-and-packages{
        /*max-height: 650px;*/
        /*overflow-y: scroll;*/
    }
    .button-inline-input{
        padding: 0 5px 2px 3px !important;
        margin: -2px 0 0 0 !important;
    }
</style>
<script>
    $(function () {
        $(".connectedSortable").sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight",
            opacity: 0.7
        }).disableSelection();
        $('.addAcceptor').click(function () {
            let acc_block = $('.acceptor-block').first().clone();
            acc_block.find('input').first().val('');
            acc_block.find('ul').sortable({
                connectWith: ".connectedSortable",
                placeholder: "ui-state-highlight",
                opacity: 0.7
            });
            acc_block.find('ul').html('');
            $(this).parent().before(acc_block);
        });
        $('.acceptors-and-packages').on('click','.addPackage',function () {
            let pkg = $('.acceptor-block').find('li').first().clone();
            pkg.find('input').val('');
            $(this).parent().find('ul').append(pkg);
        });
        $('.acceptors-and-packages').on('click','.removePackage',function () {
            let empty_form = true;
            $(this).parent().find('input').each(function(){
                if($(this).val()!==''){
                    empty_form = false;
                }
            });
            if(empty_form || confirm('Форма не пуста. Все равно удалить?')) {
                if ($('.packages-list li').length > 1) {
                    $(this).parent().remove();
                } else {
                    $(this).parent().find('input').val('');
                }
            }
        });
    });
</script>
<?php echo $form->errorSummary($products_model); ?>

<?php

$row_params = array('class' => 'wide-field', 'maxlength' => 255);

$form->rowHiddenField($products_model, 'id', array(), 'wide-label');
$form->rowHiddenField($products_model, 'taskid', array(), 'wide-label');

//$form->rowTextField($products_model, 'name', $row_params, 'wide-label');
//$form->rowTextField($products_model, 'amount', $row_params, 'wide-label');
//$form->rowTextField($products_model, 'units', $row_params, 'wide-label');
//$form->rowTextField($products_model, 'acceptor', $row_params, 'wide-label');
?>
<div style="clear: both"></div>
<div class="acceptors-and-packages">
<div class="acceptor-block">
    <input type="text" class="product-acceptor" placeholder="получатель"><button type="button" class="ui-corner-all  ui-button ui-widget button-inline-input addPackage" >+</button><br />
    <ul class="connectedSortable packages-list">
        <li class="ui-state-default"><input class="product-package" type="text" placeholder="изделие"><br/><input class="product-package-amount mini-text-fld" type="text" placeholder="количество"/> <select class="product-package-units">
                <option>к-т</option>
                <option>шт</option>
                <option>мм</option>
                <option>см</option>
                <option>м</option>
                <option>мл</option>
                <option>л</option>
                <option>г</option>
                <option>кг</option>
            </select> <button type="button" class="ui-corner-all  ui-button ui-widget removePackage button-inline-input" style="color: red">&times;</button></li>
    </ul>

</div>
<div class="acceptor-block">
    <button type="button" class="ui-corner-all  ui-button ui-widget addAcceptor button-inline-input">+</button>
</div>
</div>


