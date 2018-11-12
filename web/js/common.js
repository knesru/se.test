function tableToArray(sourceSelector, destinationSelector, newObj) {
    if(typeof newObj === 'undefined'){
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
    for(let line = 0; line<obj.data.length; line++){
        composed_data[line] = {};
        for(let col=0;col<newObj.colModel.length;col++){
            colname = newObj.colModel[col].title;
            // console.log(obj.data[line][col]);
            newObj.colModel[col].dataIndx = colname;
            if(typeof obj.data[line][col] !== 'undefined') {
                composed_data[line][colname] = obj.data[line][col];
            } else {
                composed_data[line][colname] = null;
            }
        }
    }

    newObj.dataModel = {data: composed_data};

    console.log(newObj.colModel);
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
        selectItem: { on: true }, //custom option
        highlightText: { on: true }, //custom option
        minLength: 2
    }).focus(function () {
        //open the autocomplete upon focus
        $(this).autocomplete("search", "");
    })
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
function formatDate(format){
    let date = new Date();
    let month = date.getMonth()+1;
    let day = date.getDate();
    let hour = date.getHours();
    let minute = date.getMinutes();
    let second = date.getSeconds();
    format = format.replace(/Y/,date.getFullYear());
    format = format.replace(/m/,'00'.slice(month.toString().length)+month);
    format = format.replace(/d/,'00'.slice(day.toString().length)+day);
    format = format.replace(/H/,'00'.slice(hour.toString().length)+hour);
    format = format.replace(/i/,'00'.slice(minute.toString().length)+minute);
    format = format.replace(/s/,'00'.slice(second.toString().length)+second);
    return format;
}