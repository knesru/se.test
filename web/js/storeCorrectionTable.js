let StoreCorrectionTableColumnModel = [
    {
        title: "Сдано",
        dataIndx: 'delivered',
        dataType: "integer",
        align: "right",
        filter: {
            type: 'textbox',
            condition: 'between',
            listeners: ['change']
        }
    },
];
let StoreCorrectionTableDataModel = {
    recIndx: "id", //primary key
    location: "remote",
    sorting: "remote",
    dataType: "JSON",
    method: "POST",
    sortIndx: "priority",
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
        settings.data += 'showall=' + !!$('#showAll').is(":checked");
    }
};
let StoreCorrectionTable = {
    scrollModel: {autoFit: true, horizontal: false},
    pageModel: getPageModel(),
    stringify: false, //for PHP
    dataModel: StoreCorrectionTableDataModel,
    colModel: StoreCorrectionTableColumnModel,
    selectionModel: {
        type: 'row',
        mode: 'single',
        fireSelectChange: true
    },
    filterModel: {on: true, mode: "AND", header: false},
    toolbar: {
        items: [

        ]
    },
    showTitle: false,
    numberCell: {show: false},
    columnBorders: true
};

