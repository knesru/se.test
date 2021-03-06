function tableToArray(sourceSelector, destinationSelector, newObj) {
    if (typeof newObj === 'undefined') {
        newObj = {};
    }
    let tbl = $(sourceSelector);
    tbl.find('th').each(function () {
        $(this).replaceWith('<td>' + $(this).find('a').text() + '</td>');
    });
    tbl.find('tbody').prepend(tbl.find('thead').find('tr'));
    tbl.find('thead').remove();
    //tbl.find('tbody').replaceWith(tbl.find('tbody').find('tr'));
    tbl.find('tr').removeClass('odd').removeClass('even');
    let obj = $.paramquery.tableToArray(tbl);
    //let newObj = {};
    //return;
    // console.log('data',obj.data);
    // console.log('model',obj.colModel);

    if (typeof newObj.colModel === 'undefined') {
        newObj.colModel = obj.colModel;
    }

    let composed_data = [];
    for (let line = 0; line < obj.data.length; line++) {
        composed_data[line] = {};
        for (let col = 0; col < newObj.colModel.length; col++) {
            colname = newObj.colModel[col].title;
            // console.log(obj.data[line][col]);
            newObj.colModel[col].dataIndx = colname;
            if (typeof obj.data[line][col] !== 'undefined') {
                composed_data[line][colname] = obj.data[line][col];
            } else {
                composed_data[line][colname] = null;
            }
        }
    }

    newObj.dataModel = {data: composed_data};

    //console.log(newObj.colModel);
    newObj = $(destinationSelector).pqGrid(newObj);
    tbl.css("display", "none");
    return newObj;
}

function autoCompleteEditor(ui) {
    var $inp = ui.$cell.find("input");
    var url = ui.column.editor.url;

    //initialize the editor
    $inp.autocomplete({
        appendTo: ui.$cell, //for grid in maximized state.
        source: url,
        selectItem: {on: true}, //custom option
        highlightText: {on: true}, //custom option
        minLength: 2
    }).focus(function () {
        //open the autocomplete upon focus
        $(this).autocomplete("search", "");
    })
}

function pqDatePicker(ui) {
    let $this = $(this);
    let d = new Date();
    let rng = d.getFullYear() - 2015;
    $this
    //.css({ zIndex: 3, position: "relative" })
        .datepicker({
            yearRange: "-"+rng+":+10", //25 years prior to present.
            changeYear: true,
            changeMonth: true,
            // showButtonPanel: true,
            onClose: function (evt, ui) {
                $(this).focus();
            }
        });
    //default From date
    $this.filter(".pq-from").datepicker("option", "defaultDate", new Date("01/01/1996"));
    //default To date
    $this.filter(".pq-to").datepicker("option", "defaultDate", new Date("12/31/1998"));
}

function dateEditor(ui) {
    var $inp = ui.$cell.find("input"),
        $grid = $(this),
        validate = function (that) {
            var valid = $grid.pqGrid("isValid", {
                dataIndx: ui.dataIndx,
                value: $inp.val(),
                rowIndx: ui.rowIndx
            }).valid;
            if (!valid) {
                that.firstOpen = false;
            }
        };

    //initialize the editor
    $inp
        .on("input", function (evt) {
            validate(this);
        })
        .datepicker({
            changeMonth: true,
            changeYear: true,
            showAnim: '',
            dateFormat: 'yy-m-d',
            onSelect: function () {
                this.firstOpen = true;
                validate(this);
            },
            beforeShow: function (input, inst) {
                return !this.firstOpen;
            },
            onClose: function () {
                this.focus();
            }
        });
}

function formatDate(format) {
    let date = new Date();
    let month = date.getMonth() + 1;
    let day = date.getDate();
    let hour = date.getHours();
    let minute = date.getMinutes();
    let second = date.getSeconds();
    format = format.replace(/Y/, date.getFullYear());
    format = format.replace(/m/, '00'.slice(month.toString().length) + month);
    format = format.replace(/d/, '00'.slice(day.toString().length) + day);
    format = format.replace(/H/, '00'.slice(hour.toString().length) + hour);
    format = format.replace(/i/, '00'.slice(minute.toString().length) + minute);
    format = format.replace(/s/, '00'.slice(second.toString().length) + second);
    return format;
}

function pf(n, t1, t2, t5) {
    if (typeof n === "number") {
        if (n % 100 > 10 && n % 100 < 20) {
            return t5;
        }
        if (n % 10 === 1) {
            return t1;
        }
        if (n % 10 === 2 || n % 10 === 3 || n % 10 === 4) {
            return t2;
        }
        return t5;
    }
    return t5;
}

function renderDateOnly(ui) {
    //return "hello";
    let cellData = ui.cellData;
    if (cellData) {
        return $.datepicker.formatDate('dd-mm-yy', new Date(cellData));
    }
    else {
        return "";
    }
}

function renderShortText(ui, add_class) {
    let cellData = ui.cellData;
    if(cellData===null){
        cellData = '';
    }
    if(typeof add_class === 'undefined' || add_class===null){
        add_class = '';
    }
    return '<div class="shorttext folded-text '+add_class+'" title="'+cellData+'">'+cellData.replace(/\n/g,'<br/>')+'</div>';
}

function clearFilter() {
    //userLog('Очистил фильтр','log');
    $(this).parents('.pq-grid').find('.pq-grid-header').find('input, select, textarea').each(function () {
        // Don't bother checking the field type, just check if property exists
        // and set it
        if (typeof(this.defaultChecked) !== "undefined")
            this.checked = this.defaultChecked;
        if (typeof(this.defaultValue) !== "undefined")
            $(this).val($(this).attr('defaultValue'));

        // Try to find an option with selected attribute (not property!)
        var defaultOption = $(this).find('option[selected]');
        // and fallback to the first option
        if (defaultOption.length === 0)
            defaultOption = $(this).find('option:first');
        // if no option was found, then it was not a select
        if (defaultOption.length > 0)
            this.value = defaultOption.attr('value');
        $(this).change();
    });
    $(this).parents('.pq-grid').find('.filterValue').each(function(){
        let changed = $(this).val()!=$(this).attr('defaultValue');
        $(this).val($(this).attr('defaultValue'));
        if(changed){
            $(this).keyup();
        }
    });
}


function userAction(params){
    let defaultParams = {
        action: 'general',
        partnumber: '',
        requestid: '',
        id: '',
        message: ''
    }
}
function userLog(action, severity, element, result) {
    if(typeof result === 'undefined'){
        result = '';
    }
    if(typeof severity === 'undefined'){
        severity = 'log';
    }
    if(typeof element === 'undefined'){
        element = '';
    }
    if(typeof action === 'undefined'){
        return;
    }
    let hl_class = '';
    if(severity==='info') {
        hl_class = 'ui-state-highlight';
        console.info(action, element, result);
    }
    if(severity==='log') {
        hl_class = '';
        console.log(action, element, result);
    }
    if(severity==='error') {
        hl_class = 'ui-state-error';
        console.error(action, element, result);
    }
    let msg = action;
    if (typeof msg !== 'string' && isNaN(msg)) {
        msg = JSON.stringify(msg);
    }
    if(msg.length>0) {
        saveActionHistory({
            'description': msg,
            'severity': severity
        });
    }
    footerLog(getDateTime()+' | '+msg,100,severity);
}

function footerLog(message,maxLogLength,severity){
    let hl_class = '';
    if(severity==='info') {
        hl_class = 'ui-state-highlight';
    }
    if(severity==='log') {
        hl_class = '';
    }
    if(severity==='error') {
        hl_class = 'ui-state-error';
    }
    let $footer = $('#footer');
    if($footer.find('a').length>0){
        $footer.html('').css({'text-align':'left'}).resizable({
            handles: "s",
            helper: "ui-resizable-helper"
        }).css({'height':60});
    }
    $footer.prepend('<div class="userlog_message '+hl_class+'">'+message+'</div>');
    $footer.find('.userlog_message:gt('+maxLogLength+')').remove();
}


function getDateTime() {
    let now     = new Date();
    let year    = now.getFullYear();
    let month   = now.getMonth()+1;
    let day     = now.getDate();
    let hour    = now.getHours();
    let minute  = now.getMinutes();
    let second  = now.getSeconds();
    if(month.toString().length == 1) {
        month = '0'+month;
    }
    if(day.toString().length == 1) {
        day = '0'+day;
    }
    if(hour.toString().length == 1) {
        hour = '0'+hour;
    }
    if(minute.toString().length == 1) {
        minute = '0'+minute;
    }
    if(second.toString().length == 1) {
        second = '0'+second;
    }
    let dateTime = year+'.'+month+'.'+day+' '+hour+':'+minute+':'+second;
    return dateTime;
}

function getFormData(form) {
    let rawJson = form.serializeArray();
    let model = {};

    $.map(rawJson, function (n, i) {
        model[n['name']] = n['value'];
    });


    return model;
}

function generalAjaxAnswer(result,msg,silent){
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_LOG = 'log';
    let message_body;
    let error_list = [];
    if(typeof msg==="undefined"){
        msg = false;
    }
    if(typeof result==="undefined"){
        userLog('Ответ не распознан',TYPE_ERROR);
        return false;
    }
    if(typeof silent==="undefined"){
        silent = false;
    }

    if(typeof result.success !== "undefined") {
        // if(result.success){
        if(typeof result.message!=="undefined"){
            message_body = result.message;
        }
        if(typeof result.errors === "object"){
            for(let errindex in result.errors){
                if(result.errors.hasOwnProperty(errindex)){
                    if(typeof result.errors[errindex] === 'object'){
                        for(let i=0;i<result.errors[errindex].length;i++){
                            error_list.push(result.errors[errindex][i]);
                        }
                    }else if(typeof result.errors[errindex] === 'string'){
                        error_list.push(result.errors[errindex]);
                    }
                }
            }
            message_body = error_list.join("<br/>\n");
        }
        if(result.success){
            if((msg===true || msg==='success') && message_body!=''){
                showMessage(message_body);
            }
            if(!silent) {
                userLog(message_body);
            }
        }else {
            if(msg===true || msg==='error'){
                showMessage(message_body);
            }
            if(!silent) {
                userLog(message_body, TYPE_INFO);
            }
        }

        return true;
        // }
    }
    console.log('Неизвестно, был ли ответ успешен');
    return false;
}

function saveActionHistory(params) {
    $.ajax({
        dataType: "json",
        type: "POST",
        async: true,
        url: "/actionhistory/create", //for ASP.NET, java
        data: {Actionhistory: params},
        success: function (result) {
            console.log(result);
        },
        error: function(err){
            console.error(err.responseText);
        }
    });
}

function createRow() {
    let $tr = $(this).closest('tr');
    let $grid = $tr.closest('.pq-grid');
    let rowIndx = $grid.pqGrid('getRowIndx',{$tr: $tr}).rowIndx;
    let row = $grid.pqGrid('getRowData', {rowIndx: rowIndx});
    requestsAction('create',row['id']);
}

function deleteRow() {
    let $tr = $(this).parents('tr');
    let grid = $tr.closest('.pq-grid').pqGrid('getInstance').grid;
    let rowIndx = grid.getRowIndx({$tr: $tr}).rowIndx;
    grid.addClass({rowIndx: rowIndx, cls: 'pq-row-delete' });
    let row = grid.getRowData({rowIndx: rowIndx});
    //userLog('Удаляю '+(row['priority']?'приоритетный ':'')+'компонент '+row['partnumber']+', строка '+row['id']+'...');
    if(row['priority']) {
        if (!confirm('Внимание, удаляется строка ' + row['id'] + ' компонента ' + row['partnumber'] + ' с высоким приоритетом. Продолжить?')) {
            //- не выводить сообщение об отмене удаления компонента. "Отменил удаление компонента 123, строки 58"
            // userLog('Отменил удаление компонента ' + row['partnumber'] + ', строки ' + row['id']);
            return;
        }
        //userLog('Подтвердил удаление приоритетного компонента ' + row['partnumber'] + ', строки ' + row['id']);
    }else {
        if (!confirm('Удалить строку ' + row['id'] + '?')) {
            //- не выводить сообщение об отмене удаления компонента. "Отменил удаление компонента 123, строки 58"
            // userLog('Отменил удаление компонента ' + row['partnumber'] + ', строки ' + row['id']);
            return;
        }
        //userLog('Подтвердил удаление компонента ' + row['partnumber'] + ', строки ' + row['id']);
    }
    $.ajax({
        dataType: "json",
        type: "POST",
        async: true,
        beforeSend: function (jqXHR, settings) {
            grid.showLoading();
        },
        url: "/toAssembly/removecomponent", //for ASP.NET, java
        data: {id: [row['id']]},
        success: function (result) {
            if(result.success){
                grid.deleteRow({ rowIndx: rowIndx });
                userLog('Успешно удален компонент '+result.pn);
                grid.commit();
                grid.history({method: 'reset'});
            }else{
                //grid.removeClass({ rowData: rowData, cls: 'pq-row-delete' });
                grid.rollback();
                userLog(result.error,'error');
            }
        },
        error: function(err){
            grid.rollback();
            userLog(err.responseText,'error');
        },
        complete: function () {
            grid.hideLoading();
        }
    });
}

function changePriority(evt) {
    let $tr = $(this).parents('tr');
    let $grid = $tr.closest('.pq-grid');
    let rowIndx = $grid.pqGrid('getRowIndx',{$tr: $tr}).rowIndx;
    let priority = $(this).hasClass('change-priority-up')?1:0;
    console.log($(this));
    $grid
        .pqGrid( "updateRow", {
            rowIndx: rowIndx,
            checkEditable: false,
            row: { 'priority': priority }
        });
    evt.stopPropagation();
    evt.stopImmediatePropagation();
    return false;
}

function loadUserHistory(){
    $.ajax({
        dataType: "json",
        type: "POST",
        async: true,
        url: "/actionhistory/list", //for ASP.NET, java
        success: function (result) {
            if(typeof result!=="undefined" &&
                typeof result.success!=="undefined" &&
                result.success &&
                typeof result.data!=="undefined" &&
                result.data.length>0
            ){
                for(let i=0;i<result.data.length; i++){
                    footerLog(result.data[i].created_at.replace(/-/g,'.')+' | '+result.data[i].description,100,result.data[i].severity);
                }
            }else{
                footerLog(getDateTime()+' | Не удалось загрузить историю действий',100,'error');
            }
        },
        error: function(err){
            footerLog(getDateTime()+' | Не удалось загрузить историю действий. Возможная причина:'+err.responseText,100,'error');
        }
    });
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname,csubname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            let out = c.substring(name.length, c.length);
            if(typeof csubname !== 'undefined'){
                let cobj = JSON.parse(out);
                if(typeof cobj === 'object'){
                    if (typeof cobj[csubname]!=='undefined'){
                        return cobj[csubname];
                    }
                }
            }
            return out;
        }
    }
    return "";
}

function showMessage(message, type) {
    if(typeof type === 'undefined'){
        type = 'info';
    }
    showDialogMessage({title: type, message: message});
}
function showWarning(message) {
    showMessage(message,'warning');
}
function showError(message) {
    showMessage(message,'error');
}
function showDialogMessage(params) {
    defaultParams = {
        header: 'info',
        type: 'info',
        message: 'info',
        buttons: {
            ok: function () {
                $(this).dialog("close");
            }
        }
    };
    params = $.extend(defaultParams,params);
    if($("#popup-dialog-message").length!=0) {
        $("#popup-dialog-message").html(params.message).removeClass('ui-state-error').removeClass('ui-state-highlight');
        if (params.type === 'warning') {
            $("#popup-dialog-message").addClass('ui-state-highlight');
        }
        if (params.type === 'error') {
            $("#popup-dialog-message").addClass('ui-state-error');
        }
        $("#popup-dialog-message").dialog({
            title: tdt(params.title),
            buttons: params.buttons,
            modal: true
            // dialogClass: "ui-state-highlight",
            // classes: {
            //     "ui-dialog": "ui-state-highlight",
            //     "ui-dialog-title": "ui-state-highlight"
            // }
        }).dialog("open");
    }else{
        alert(params.message);
    }
}
//translate dialog title
function tdt(title) {
    let titles = {
        'error': 'ошибка',
        'info': 'инфо',
        'warning': 'предупреждение',
    };
    if(typeof titles[title] !== 'undefined'){
        return titles[title];
    }
    return title;
}