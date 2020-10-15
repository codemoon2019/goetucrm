/**
 * Created by Jianfeng Li on 2017/4/23.
 */

import React from "react";
import ReactDOMServer from 'react-dom/server';
import PropTypes from "prop-types";
import LoadingComponent from "./loading.component";
import swal from "sweetalert2";
import _ from "lodash";
import toastr from "toastr";
import {translate} from "react-i18next";
import * as Config from "../../react/config";

class DataTableComponent extends React.PureComponent {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        //console.log("data table: componentDidMount.");
        this.initToastrConfig();
        this.initDataTable();
        this.initDataTableEvents();
    }

    initToastrConfig() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    }

    /**
     *  Init DataTable.
     */
    initDataTable() {
        let {url, method, params} = this.props;
        let {multiple, selectedClass, selectedHighLight, multiSelectedClass} = this.props;
        let {basicSearch} = this.props;

        if (basicSearch) this.handleBasicSearch(basicSearch);
        let defColValues = this.initColValues();
        let orderConfig = this.initOrderConfig();
        /* DataTable processing dom */
        let processingHtml = ReactDOMServer.renderToStaticMarkup(<LoadingComponent show={true} size={"5x"}/>);

        let that = this;
        this.table = $(this.dataTable).find("table")
            .on('processing.dt', function (e, settings, processing) {
                that.setState({
                    loading: !!processing
                });
            })
            .DataTable({
                serverSide: true,
                language: {
                    processing: processingHtml,
                },
                processing: true,
                responsive: true,
                deferRender: true,
                columns: defColValues,
                order: orderConfig,
                select: {
                    style: multiple ? 'multi' : "single",
                    className: multiple ? `${selectedClass} ${multiSelectedClass}` : selectedClass,
                    selector: selectedHighLight,
                },
                ajax: {
                    url: Config.APP_URL + url,
                    type: method ? method : "post",
                    data: (d) => {
                        for (let property in params) {
                            if (params.hasOwnProperty(property)) {
                                d[property] = params[property];
                            }
                        }

                        let bs_params = $(this.dataTable).data('basicSearch');
                        //   Add search parameters to the data object sent to the server
                        if (bs_params) {
                            d.bs_params = bs_params;
                        }
                        d._token = Config.TOKEN;
                        d.page = d.start / d.length + 1;
                        d.perPage = d.length;
                    },
                    dataFilter: function (data) {
                        let jsonDate = JSON.parse(data);
                        jsonDate.recordsTotal = jsonDate.total;
                        jsonDate.recordsFiltered = jsonDate.total;
                        jsonDate.DT_RowID = jsonDate.id;
                        return JSON.stringify(jsonDate); // return JSON string
                    }
                },
                drawCallback: function (settings) {
                    $(that.dataTable).find(".btn-remove").on("click", function (event) {
                        event.preventDefault();
                        let selectedItem = that.table.row($(this).closest("tr")).data();
                        that.destroy(selectedItem);
                        return false;
                    });

                    $(that.dataTable).find(".btn-edit").on("click", function (event) {
                        event.preventDefault();
                        let selectedItem = that.table.row($(this).closest("tr")).data();
                        that.edit(selectedItem);
                        return false;
                    });
                },
            });
    }


    initDataTableEvents() {
        const {multiple} = this.props;
        let that = this;

        if (multiple) {
            that.table.on('select', function (e, dt, type, indexes) {
                if (type === 'row') {
                    let selectedIds = that.table.rows(indexes).data().pluck("id");
                    // do something with the ID of the selected items
                    _.forEach(selectedIds, function (id) {
                        $(that.dataTable).find(`.cb_${id}`).prop("checked", true);
                    });
                }
            });

            that.table.on('deselect', function (e, dt, type, indexes) {
                if (type === 'row') {
                    let deSelectedIds = that.table.rows(indexes).data().pluck("id");
                    // do something with the ID of the selected items
                    _.forEach(deSelectedIds, function (id) {
                        $(that.dataTable).find(`.cb_${id}`).prop("checked", false);
                    });
                }
            });
            let allCheckbox = $(that.dataTable).find(".row-all");

            allCheckbox.on("change", function () {
                if (this.checked) {
                    allCheckbox.prop("checked", true);
                    that.table.rows().select();
                } else {
                    allCheckbox.prop("checked", false);
                    that.table.rows().deselect();
                }
            });
        }
    }

    initOrderConfig() {
        const {defCol} = this.props;
        const {showTimestamp, multiple} = this.props;

        let orderIndex = Object.keys(defCol).length - 1;

        if (showTimestamp) {
            orderIndex += 2;
        }

        return showTimestamp ? [[orderIndex, "desc"]] : (multiple ? [[0, "desc"]] : []);
    }

    /**
     *  Generate actions url.
     */
    generateActionsUrl(data, type, row) {
        const {resourceUrl, showEditBtn, showDeleteBtn} = this.props;
        let actions = [];

        // if resource url and showEditBtn is true, show the edit button
        if (resourceUrl && showEditBtn) {
            actions.push(
                <li className="list-inline-item" key="edit">
                    <a className="btn btn-sm btn-primary btn-edit btn-icon btn-round" onClick={() => {
                        this.edit(row);
                    }}>
                        <i className="fa fa-pencil fa-fw text-light"/>
                    </a>
                </li>
            );
        }

        // if resource url and showDeleteBtn is true, show the delete button
        if (resourceUrl && showDeleteBtn) {
            actions.push(
                <li className="list-inline-item" key="delete">
                    <a className="btn btn-sm btn-danger btn-remove btn-icon btn-round" data-id={row["id"]}><i
                        className="fa fa-trash fa-fw text-light"/></a>
                </li>
            );
        }
        return (
            <ul className="list-inline">
                {actions}
            </ul>
        );

    }

    /**
     *  Init Defined Columns' Values.
     */
    initColValues() {
        let {defCol, resourceUrl, showEditBtn, showDeleteBtn} = this.props;
        let {showTimestamp, multiple} = this.props;
        let defColValues = [];

        if (multiple) {
            defColValues[0] = {
                "searchable": false,
                "orderable": false,
                "render": function (data, type, full, meta) {
                    return ReactDOMServer.renderToStaticMarkup(
                        <div className="mr-1">
                            <div className="form-check">
                                <label className="form-check-label">
                                    <input type="checkbox" name="id" className={`row-checkbox cb_${full.id}`}
                                           disabled={"disabled"}
                                    />
                                    <span className="form-check-sign"/>
                                </label>
                            </div>
                        </div>
                    );
                }
            };
        }

        for (let text in defCol) {
            if (defCol.hasOwnProperty(text)) {
                // set up the default orderable and searchable as false
                defColValues.push(Object.assign({}, {"orderable": false, "searchable": false}, defCol[text]));
            }
        }

        if (showTimestamp) {
            // set up default columns : "Created At"  and  "Last Updated"
            defColValues.push(
                {"data": "created_at", "orderable": true, "searchable": false},
                {"data": "updated_at", "orderable": true, "searchable": false},
            );
        }

        if (resourceUrl && (showDeleteBtn || showEditBtn)) {
            defColValues.push({
                "data": "Actions",
                "width": "160px",
                "orderable": false,
                "searchable": false,
                "render": (data, type, row) => {
                    let actionsUrl = this.generateActionsUrl(data, type, row);
                    actionsUrl = ReactDOMServer.renderToStaticMarkup(actionsUrl);
                    return actionsUrl;
                }
            });
        }

        return defColValues;
    }


    /**
     *  Init Defined Columns' Texts.
     */
    initColTexts() {
        let {t} = this.props;
        let {defCol, resourceUrl, showEditBtn, showDeleteBtn} = this.props;
        let {showTimestamp, multiple} = this.props;
        let defColTexts = [];

        defColTexts = defColTexts.concat(Object.keys(defCol));

        if (showTimestamp) {
            defColTexts.push(t("created_at"), t("updated_at"));
        }

        let defColTextList = defColTexts.map((value, index) => {
            return (
                <th key={index}>
                    {t(value)}
                </th>
            );
        });

        if (multiple) {
            defColTextList.splice(0, 0, (
                <th key={"all"}>
                    <div className="form-check">
                        <label className="form-check-label">
                            <input type="checkbox" className="row-all"/>
                            <span className="form-check-sign"/>
                        </label>
                    </div>
                </th>
            ));
        }

        if (resourceUrl && (showDeleteBtn || showEditBtn)) {
            defColTextList.push(
                <th key={defColTextList.length + 1}>
                    {t("actions")}
                </th>
            );
        }

        return defColTextList;
    }

    /**
     *  Init buttons tool.
     */
    initButtonTools() {
        const {t} = this.props;
        const {showCreateBtn, showAttachBtn} = this.props;
        let buttonsTool = null;
        let buttonList = [];
        if (showCreateBtn) {
            buttonList.push(
                <a key="createBtn" className="btn btn-primary ml-1 text-light" onClick={() => this.create()}>
                    <i className="fa fa-plus-circle fa-lg text-light" aria-hidden="true"/>&nbsp;&nbsp;{t("new")}
                </a>
            );
        }

        if (showAttachBtn) {
            buttonList.push(
                <a key="attachBtn" className="btn btn-primary ml-1 text-light" onClick={() => this.attach()}>
                    <i className="fa fa-link fa-lg text-light" aria-hidden="true"/>&nbsp;&nbsp;{t("attach")}
                </a>
            );
        }

        const {customUrls} = this.props;
        if (customUrls.length > 0) {
            customUrls.map((urlObject, index) => {
                buttonList.push(
                    <a key={`custom-${index}`}
                       className="btn btn-primary ml-1 text-light"
                       onClick={() => {
                           this.customAction(urlObject)
                       }}
                    >
                        <i className={"text-light " + (_.isEmpty(urlObject["iconClass"]) ? "fa fa-fw fa-pencil-square-o" : urlObject["iconClass"])}/>
                        &nbsp;&nbsp;{t(urlObject["text"])}
                    </a>
                )
            });
        }

        if (buttonList.length > 0) {
            buttonsTool = (
                <div className="row">
                    <div className="col-sm-12">
                        {buttonList}
                    </div>
                </div>
            );
        }

        return buttonsTool;
    }


    /**
     * Basic search for dataTable
     *
     */
    handleBasicSearch(options) {
        let {sBtn, formData} = options;
        sBtn.addEventListener('click', (e) => {
            e.preventDefault();

            /**  Get search form data
             *   DataFormat
             *   {
             *     name
             *     filterable
             *     searchable
             *     value : string || array
             *   }
             *
             *   value can be a object contains
             *   {
             *      value : array
             *      type
             *      operation
             *   }
             *  */
            let basicSearch = $(formData).serializeArray().map(item => {
                let el = document.getElementsByName(item.name)[0];
                let searchable = false, filterable = true;
                let operation, type = null;

                if (!_.isNil(el.dataset.basicSearch) && el.dataset.basicSearch === "true") {
                    searchable = true;
                    filterable = false;
                }

                if (!_.isNil(el.dataset.basicOperation)) {
                    operation = el.dataset.basicOperation;
                }

                if (!_.isNil(el.dataset.basicType)) {
                    type = el.dataset.basicType;
                }

                if (item.name.includes("[]"))
                    item.name = item.name.replace('[]', '');

                return {
                    name: item.name,
                    searchable: searchable,
                    filterable: filterable,
                    value: item.value,
                    operation: operation,
                    type: type,
                }
            }).reduce((result, item) => {
                const length = result.length;
                if (length > 1 && (result[length - 1].name === item.name)) {
                    result[length - 1].value = {
                        value: [result[length - 1].value, item.value],
                        operation: item.operation,
                        type: item.type
                    };
                } else {
                    let {operation, type, ...temp} = item;
                    result.push(temp);
                }
                return result;
            }, []);
            $(this.dataTable).data('basicSearch', basicSearch);
            this.table.draw();
        }, true);
    }

    customAction(urlObject) {
        const {t, selectedClass} = this.props;
        let selectedId = _.last(this.table.rows(`.${selectedClass}`).data().pluck("id"));
        if (_.isNil(selectedId)) {
            toastr["error"](t("selectRow"));
        } else {
            const {multiple} = this.props;
            if (multiple) {
                let selectedIds = this.table.rows(`.${selectedClass}`).data().pluck("id");
                if (urlObject.callback)
                    urlObject.callback({
                        "url": `${Config.APP_URL}/${urlObject["url"]}?rid=${selectedIds.join()}${_.isEmpty(urlObject["query"]) ? "" : `&${urlObject["query"]}`}`,
                        "text": urlObject["text"],
                        "table": this.table,
                    });
                else
                    window.location.href = `${Config.APP_URL}/${urlObject["url"]}?rid=${selectedIds.join()}${_.isEmpty(urlObject["query"]) ? "" : `&${urlObject["query"]}`}`;
            } else {
                if (urlObject.callback)
                    urlObject.callback({
                        "url": `${Config.APP_URL}/${urlObject["url"]}?rid=${selectedId}${_.isEmpty(urlObject["query"]) ? "" : `&${urlObject["query"]}`}`,
                        "text": urlObject["text"],
                        "table": this.table,
                    });
                else
                    window.location.href = `${Config.APP_URL}/${urlObject["url"]}?rid=${selectedId}${_.isEmpty(urlObject["query"]) ? "" : `&${urlObject["query"]}`}`;
            }
        }
    }

    /**
     *  "New Button" Create handler.
     */
    create() {
        const {create, resourceUrl} = this.props;
        if (create) {
            create(this.table);
        } else {
            window.location.href = Config.APP_URL + resourceUrl + "/create";
        }
    }

    /**
     *  "Attach Button" handler.
     */
    attach() {
        const {attach, selectedClass} = this.props;
        if (attach) {
            let itemIds = this.table.rows(`.${selectedClass}`).data().pluck("id");
            attach(itemIds);
        }
    }

    edit(row) {
        const {edit, resourceUrl} = this.props;
        if (edit) {
            edit(row, this.table);
        } else {
            window.location.href = Config.APP_URL + resourceUrl + "/" + row["id"] + "/edit";
        }
    }

    destroy(row) {
        const {t, resourceUrl} = this.props;
        let rowId = row["id"];
        swal({
            title: `${t("deleteTitle")}`,
            text: `${t("deleteText")}`,
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: `${t("cancel")}`,
            cancelButtonClass: "btn btn-secondary btn-lg",
            confirmButtonText: `${t("delete")}`,
            confirmButtonClass: "btn btn-primary btn-lg",
            showLoaderOnConfirm: true,
            buttonsStyling: false,
            allowOutsideClick: false,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    axios.delete(Config.APP_URL + resourceUrl + "/" + rowId).then((response) => {
                        resolve(response);
                    }).catch((error) => {
                        console.log("destroy error", error);
                        reject("Some wrong.");
                    });
                })
            }
        }).then((result) => {
            if (result.value) {
                swal({
                    title: `${t("deleted")}!`,
                    type: 'success',
                    confirmButtonText: `${t("confirm")}`,
                    confirmButtonClass: "btn btn-primary btn-lg",
                    buttonsStyling: false,
                });
                this.table.ajax.reload();
                this.setState({loading: false});
            }
        }).catch((error) => {
            this.setState({loading: false});
        });
    }

    render() {
        const {t} = this.props;
        let {title, containerStyle} = this.props;

        let defColTexts = this.initColTexts();
        let buttonsTool = this.initButtonTools();

        return (
            <div className={"box box-warning " + containerStyle} ref={(dataTable) => {
                this.dataTable = dataTable;
            }}>
                <div className="box-header with-border">
                    <h3 className="box-title">{t(title)}</h3>
                    <div className="box-tools pull-right">
                        <button type="button" className="btn btn-sm btn-box-tool" data-widget="collapse">
                            <i className="fa fa-minus"/>
                        </button>
                    </div>
                </div>
                <div className="box-body">
                    {buttonsTool}
                    <br/>
                    <div className="row">
                        <table className="table table-striped table-bordered table-hover"
                               width="100%"
                        >
                            <thead>
                            <tr>
                                {defColTexts}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                {defColTexts}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        );
    }
}

DataTableComponent.propTypes = {
    containerStyle: PropTypes.string,
    defCol: PropTypes.object.isRequired,
    url: PropTypes.string.isRequired,
    params: PropTypes.object,
    resourceUrl: PropTypes.string,
    customUrls: PropTypes.array,
    showEditBtn: PropTypes.bool,
    showDeleteBtn: PropTypes.bool,
    showCreateBtn: PropTypes.bool,
    showAttachBtn: PropTypes.bool,
    title: PropTypes.string,
    method: PropTypes.string,
    multiple: PropTypes.bool,
    selectedClass: PropTypes.string,
    multiSelectedClass: PropTypes.string,

    create: PropTypes.func,
    edit: PropTypes.func,
    attach: PropTypes.func,
    showTimestamp: PropTypes.bool,
};

DataTableComponent.defaultProps = {
    containerStyle: "",
    defCol: {},
    params: {},
    customUrls: [],
    basicSearch: null,
    showEditBtn: false,
    showDeleteBtn: false,
    showCreateBtn: false,
    showAttachBtn: false,
    method: "GET",
    showTimestamp: true,
    multiple: false,
    selectedClass: "selected",
    multiSelectedClass: "multiple-selected",
    selectedHighLight: "",
};

export default translate()(DataTableComponent);