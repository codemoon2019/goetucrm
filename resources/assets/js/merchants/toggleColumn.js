let hiddenCols = [];

function redrawTable(tbl) {
    let table = $(tbl).DataTable();
    table.columns(hiddenCols).visible(false, false);
    table.columns.adjust().draw(false); // adjust column sizing and redraw
}

function toggleCols(tbl) {
    let table = $(tbl).DataTable();
    let curActive = $('.tabs-rectangular li a').parents('.tabs-rectangular');
    let curActiveId = curActive.find('li.active a').attr('id');
    let defaultHidden = ["Merchant ID", "Partners", "Merchant Name", "Contact Person"];
    let list = 'user-dept';
    if ($('#branch-list').length) {
        defaultHidden = ["Branch ID", "Owner", "Branch Name", "Contact Person"];
    }
    if ($('#leads-table').length) {
        defaultHidden = ["Lead ID", "Company Name", "Contact Person"];
    }
    if ($('#prospects-table').length) {
        defaultHidden = ["Prospect ID", "Company Name", "Contact Person"];
    }
    if (curActiveId != null) {
        defaultHidden = ["Partners", "Company Name", "Contact Person"];
        list = tbl.substr(1);
    }

    table.columns().every(function (index) {
        let column = table.column(index);
        let title = table.column(index).header();
        let i = column.index();
        let header = $(title).html();

        if (!defaultHidden.includes(header) && $('.toggle-col-' + i).length == 0) {
            $('.' + list).append('<li class="">\
                <input type="checkbox" name="toggle-cols" id="toggle-col-'+ i + '" class="toggle-vis" data-column="' + i + '" checked="checked">\
                <label for="toggle-col-'+ i + '" class="dept-name">' + header + '</label>\
                </li>');
        }

    })

    $("input.toggle-vis").on('click', function () {
        let table = $(tbl).DataTable();
        let column = table.column($(this).attr('data-column'));
        let colNum = $(this).attr('data-column');

        column.visible(!column.visible());
        $(this).attr("checked", !$(this).attr("checked"));

        if (!hiddenCols.includes(colNum)) {
            hiddenCols.push(colNum);
        } else {
            let index = hiddenCols.indexOf(colNum);
            if (index > -1) {
                hiddenCols.splice(index, 1);
            }
        }
    });
}

window.redrawTable = redrawTable;
window.toggleCols = toggleCols;