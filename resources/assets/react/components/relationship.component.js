/**
 * Created by Jianfeng Li on 2017/4/23.
 */

import React from "react"
import PropTypes from "prop-types";
import DataTableComponent from "./datatable.component";
import {translate} from "react-i18next";

class RelationshipComponent extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            primaryTable: null,
        };
    }

    create(dataTable) {
        $(".bs-modal").modal('show');
        this.setState({
            primaryTable: dataTable
        });
    }

    attach(selectedItems, ownerId) {
        const ctx = document.querySelector("#ctx").getAttribute("content");
        const {attachUrl, resourceUrl} = this.props;
        const {primaryTable} = this.state;

        let attachedIds = selectedItems.map((item, index) => {
            return item["id"];
        });
        axios.post(ctx + (attachUrl ? attachUrl : resourceUrl), {
            owner_id: ownerId,
            attach_ids: attachedIds,

        }).then(response => {
            primaryTable.ajax.reload();
        }).catch(errors => console.error(errors));

        $(".bs-modal").modal('hide');
    }

    render() {
        const {t} = this.props;
        const {url, defCol, params, title, showDeleteBtn, resourceUrl, showEditBtn, edit} = this.props;
        const {ownerId, relatedUrl, relatedDefCol, relatedParams, relatedTitle, relatedShowTimestamp} = this.props;
        return (
            <div>
                <DataTableComponent
                    t={t}
                    url={url}
                    params={params}
                    defCol={defCol}
                    title={title}
                    showCreateBtn={true}
                    create={(dataTable) => this.create(dataTable)}
                    showEditBtn={showEditBtn}
                    edit={(selectedRow, dataTable) => edit(selectedRow, dataTable)}
                    resourceUrl={resourceUrl}
                    showDeleteBtn={showDeleteBtn}
                />

                <div className="modal fade bs-modal" tabIndex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                    <div className="modal-dialog modal-lg" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h4 className="modal-title"/>
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div className="modal-body">
                                <DataTableComponent
                                    t={t}
                                    url={relatedUrl ? relatedUrl : url}
                                    params={relatedParams}
                                    defCol={relatedDefCol ? relatedDefCol : defCol}
                                    title={relatedTitle ? relatedTitle : title}
                                    showAttachBtn={true}
                                    attach={(selectedItems, dataTable) => {
                                        this.attach(selectedItems, ownerId);
                                        dataTable.ajax.reload();
                                    }}
                                    showTimestamp={relatedShowTimestamp}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

RelationshipComponent.propTypes = {
    // Main table props.
    containerStyle: PropTypes.string,
    defCol: PropTypes.object.isRequired,
    url: PropTypes.string.isRequired,
    params: PropTypes.object,
    customUrls: PropTypes.array,
    title: PropTypes.string,
    method: PropTypes.string,
    resourceUrl: PropTypes.string,
    showDeleteBtn: PropTypes.bool,
    showEditBtn: PropTypes.bool,
    edit: PropTypes.func,

    // Related table props.
    ownerId: PropTypes.any.isRequired,
    relatedDefCol: PropTypes.object,
    relatedUrl: PropTypes.string,
    relatedParams: PropTypes.object,
    relatedTitle: PropTypes.string,
    relatedMethod: PropTypes.string,
    relatedShowTimestamp: PropTypes.bool,
    attachUrl: PropTypes.string.isRequired,
};

RelationshipComponent.defaultProps = {
    containerStyle: "",
    params: {},
    customUrls: [],
    title: "",
    method: "GET",
    resourceUrl: "",
    showDeleteBtn: false,

    relatedParams: {},
    relatedTitle: "",
    relatedMethod: "GET",
    relatedShowTimestamp: false,
    attachUrl: "",
};

export default translate()(RelationshipComponent);