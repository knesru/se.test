let StoreCorrectionTableColumnModel = [
    getIdColumn(true),
    getPartnumberColumn(),
    getUserColumn(),
    getAmountColumn(),
    getDescriptionColumn(),
    getCreated_atColumn()
];
let StoreCorrectionTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "local",
    dataType: "JSON",
    method: "POST",
    sortIndx: "id",
    sortDir: "down",
    url: "/toAssembly/storeCorrectionlist",
    getData: function (response) {
        return {curPage: response.curPage, totalRecords: response.totalRecords,data: response.data};
    },
    beforeSend: function (jqXHR, settings) {
        // console.log(jqXHR);
        // console.log(settings);
        if (settings.data.length > 0) {
            settings.data += '&';
        }
        let selectedComp = $("#grid_store_correction").data('selectedComp');
        if(typeof selectedComp !== "undefined" && !isNaN(selectedComp)){
            settings.data+='id='+selectedComp;
        }
    }
};
let StoreCorrectionTable = {
    scrollModel: {autoFit: true, horizontal: false},
    height: "100%-2",
    //pageModel: getPageModel(),
    stringify: false, //for PHP
    dataModel: StoreCorrectionTableDataModel,
    colModel: StoreCorrectionTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'single',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "AND", header: false},
    rowSelect: function( event, ui ) {
        $('.shorttext').addClass('folded-text');
        ui.$tr.find('.shorttext').removeClass('folded-text');
    },
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true
};