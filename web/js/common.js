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

function renderShortText(ui) {
    let cellData = ui.cellData;
    return '<div class="shorttext folded-text" title="'+cellData+'">'+cellData+'</div>';
}

function clearFilter() {
    userLog('Очистил фильтр','log');
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
    if($('#footer').find('a').length>0){
        $('#footer').html('').css({'text-align':'left','height':'60px'});
    }
    $('#footer').prepend('<div class="'+hl_class+'">'+getDateTime()+' | '+msg+'</div>');
}


function getDateTime() {
    var now     = new Date();
    var year    = now.getFullYear();
    var month   = now.getMonth()+1;
    var day     = now.getDate();
    var hour    = now.getHours();
    var minute  = now.getMinutes();
    var second  = now.getSeconds();
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
    var dateTime = year+'.'+month+'.'+day+' '+hour+':'+minute+':'+second;
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

function generalAjaxAnswer(result){
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'error';
    const TYPE_LOG = 'error';
    if(typeof result==="undefined"){
        userLog('Ответ не распознан',TYPE_ERROR);
        return false;
    }
    if(typeof result.success !== "undefined") {
        if(result.success){
            if(typeof result.message!=="undefined"){
                userLog(result.message);
            }
            return true;
        }
    }
    userLog('Неизвестно, был ли ответ успешен',TYPE_ERROR);
    return false;
}