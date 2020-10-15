
import React from "react";
import ReactDOM from "react-dom";
import DataTableComponent from "../../../react/components/datatable.component";
import {I18nextProvider} from "react-i18next";
import i18n from "../../../react/i18n";

const el = document.querySelector("#companyTable");
if (el) {
    let defCol = {
        "company.companyName": {"data": "company_name", "searchable": true},
        "company.url": {"data": "powered_by_link", "searchable": true},
        "company.logoPath": {"data": "logo_path", "searchable": false}
    };

    ReactDOM.render(
        <I18nextProvider i18n={i18n}>
            <DataTableComponent
                url={"/admin/company"}
                defCol={defCol}
                resourceUrl={"/admin/company"}
                title={"company.list"}
                showTimestamp={false}
                showEditBtn={true}
                showCreateBtn={true}
                showDeleteBtn={false}
            />
        </I18nextProvider>,
        el
    );
}
