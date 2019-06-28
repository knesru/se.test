<?php
function createLabel($attribute){
    $model = Extcomponents::model();
    $out = $model->getAttributeLabel($attribute);
    if($model->isAttributeRequired($attribute)){
        $out.='<span class="required">*</span>';
    }
    return $out;
}
?>

<form id="fnc_form">
    <div class="form">
    <table>
        <tr>
            <td><label for="fnc_partnumber"><?php print createLabel('partnumber');?></label></td>
            <td><input type="text" id="fnc_partnumber" name="partnumber"><input type="hidden"
                                                                                name="partnumberid"
                                                                                id="fnc_partnumberid"></td>
        </tr>
        <tr>
            <td><label for="fnc_amount"><?php print createLabel('amount');?></label></td>
            <td><input type="number" id="fnc_amount" name="amount"></td>
        </tr>
        <tr>
            <td><label for="fnc_purpose"><?php print createLabel('purpose');?></label></td>
            <td><textarea id="fnc_purpose" name="purpose"></textarea></td>
        </tr>
        <tr>
            <td><label for="fnc_install_to"><?php print createLabel('install_to');?></label></td>
            <td><input type="date" id="fnc_install_to" name="install_to"></td>
        </tr>
        <tr>
            <td><label for="fnc_install_from"><?php print createLabel('install_from');?></label></td>
            <td><input type="date" id="fnc_install_from" name="install_from"></td>
        </tr>
        <tr>
            <td><label for="fnc_assembly_to"><?php print createLabel('assembly_to');?></label></td>
            <td><input type="date" id="fnc_assembly_to" name="assembly_to"></td>
        </tr>
        <tr>
            <td><label for="fnc_description"><?php print createLabel('description');?></label></td>
            <td><textarea id="fnc_description" name="description"></textarea></td>
        </tr>
        <tr>
            <td><label for="fnc_deficite"><?php print createLabel('deficite');?></label></td>
            <td><textarea id="fnc_deficite" name="deficite"></textarea></td>
        </tr>
        <tr>
            <td><label for="fnc_priority"><?php print createLabel('priority');?></label></td>
            <td><input type="checkbox" id="fnc_priority" name="priority"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><p class="note">Поля, обозначенные <span
                            class="required">*</span>, обязательны для заполнения.</p><button type="button"
                                                                                              id="fnc_submit">Сохранить</button></td>
        </tr>
    </table>

    </div>
</form><!-- form -->
<script type="application/javascript">
    $(function(){
        $('#fnc_form').find('input,textarea,select').bind('change, keyup, focus',function(){
            if($(this).hasClass('error')){
                $(this).removeClass('error');
            }
            $(this).attr('title','');
        });
        let $inp = $('#fnc_partnumber');
        $inp.autocomplete({
            source: '/component/ajaxList',
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 2,
            select: function (a,b,c) {
                // console.log(a,b,c);
                $.ajax({
                    url: '/component/ajaxComponent',
                    data: {partnumber: b.item.value},
                    success: function (res) {
                        if (typeof res === 'string') {
                            $('#fnc_partnumberid').val(res).addClass('success');
                            $('#fnc_partnumber').removeClass('warning').addClass('success').attr('title','Компонент ' +
                                'найден в STMS')
                                .tooltip();
                        }else{
                            userLog('component not');
                        }
                    }
                });
            }
        }).focus(function () {
            //open the autocomplete upon focus
            $(this).autocomplete("search", "");
        }).keyup(function(){
            $(this).addClass('warning').attr('title','Произвольный компонент').tooltip();
            $('#fnc_partnumberid').val('');
        });
        $('#fnc_submit').click(function(){
            let grid = $componentsGrid.pqGrid('getInstance').grid;
            let changes = {};
            changes.addList = [getFormData($('#fnc_form'))];
            changes.updateList = [{}];
            changes.deleteList = [{}];
            // userLog(changes);
            $.ajax({
                dataType: "json",
                type: "POST",
                async: true,
                beforeSend: function (jqXHR, settings) {
                    grid.showLoading();
                },
                url: "/toAssembly/update", //for ASP.NET, java
                data: {list: JSON.stringify(changes)},
                success: function (result) {
                    console.log(result);
                    if(typeof result.success === 'undefined'){
                        userLog('Произошла ошибка','error');
                        console.log(result);
                        return;
                    }
                    if(result.success){
                        grid.refreshDataAndView();
                        userLog('Успешно добавлен компонент '+changes.addList[0].partnumber);
                        $('#fnc_form')[0].reset();
                        $('#fnc_partnumber').removeClass('warning').removeClass('success').attr('title','');
                        $('#popup-dialog-form-new-component').dialog('close');
                    }else{
                        $('#fnc_form').find('input,textarea').attr('title','');
                        if(typeof result.data !== 'undefined' && result.data.length>0){
                            for(let i=0; i<result.data.length; i++){
                                let datum = result.data[i];
                                if(typeof datum.errors === 'object'){
                                    for(let attr in datum.errors){
                                        if(datum.errors.hasOwnProperty(attr)){
                                            let error = datum.errors[attr];
                                            $('#fnc_'+attr).addClass('error').attr('title',error.join('<br/>'))
                                                .tooltip();
                                        }
                                    }
                                }
                            }
                        }
                        if(typeof result.message !== 'undefined'){
                            showMessage(result.message);
                        }
                    }
                },
                error: function(err){
                    userLog(err.responseText,'error');
                },
                complete: function () {
                    grid.hideLoading();
                }
            });
        });
    });

</script>