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
    console.log(obj.data);
    newObj.dataModel = {data: obj.data};
    if (typeof newObj.colModel === 'undefined') {
        newObj.colModel = obj.colModel;
    }
    console.log(newObj.colModel);
    $(destinationSelector).pqGrid(newObj);
    tbl.css("display", "none");
    return newObj;
}
function autoCompleteEditor(ui) {
    var $inp = ui.$cell.find("input");

    //initialize the editor
    $inp.autocomplete({
        appendTo: ui.$cell, //for grid in maximized state.
        source: (ui.dataIndx == "books" ? books : "/pro/demos/getCountries"),
        selectItem: { on: true }, //custom option
        highlightText: { on: true }, //custom option
        minLength: 0
    }).focus(function () {
        //open the autocomplete upon focus
        $(this).autocomplete("search", "");
    });
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