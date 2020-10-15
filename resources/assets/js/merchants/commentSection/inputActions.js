import React from "react";
import PrivacySection from './privacySection';

export default class InputActions extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            firstRun : true,
            viewersTooltip : 'Control who can see this message'
        }

        this.handleShowAddFile = this.handleShowAddFile.bind(this)
        this.handleAddFile = this.handleAddFile.bind(this)
        this.handleChangePrivacy = this.handleChangePrivacy.bind(this)
        this.handleChangeOrderStatus = this.handleChangeOrderStatus.bind(this)

        this.handleChangeViewers = this.handleChangeViewers.bind(this) 
        this.handleChangleEmailReceivers = this.handleChangleEmailReceivers.bind(this) 
    }

    componentDidMount() {
        $('.with-tooltip').tooltip();
    }

    componentDidUpdate() {
        $('.with-tooltip').tooltip();

        if (this.props.attachments.length > 0 && this.state.firstRun) {
            $('#tooltip-on-show-' + this.props.subTaskDetailId).tooltip({
                'animation' : true,
                'delay' : {
                    'show' : 1000,
                    'hide' : 100
                },
                'trigger' : 'manual',
            })
    
            $('#tooltip-on-show-' + this.props.subTaskDetailId).tooltip('show')

            setTimeout(() => {
                $('#tooltip-on-show-' + this.props.subTaskDetailId).tooltip('hide')
            }, 1500)

            this.setState({
                firstRun : false
            })
        }
    }

    handleShowAddFile() {
        this.refs.attachments.click()
    }

    handleAddFile(e) {
        this.props.onAddFile(e)
    }

    handleChangePrivacy() {
        this.props.onChangePrivacy(this.refs.privacy.value)
    }

    handleChangeOrderStatus() {
        this.props.onChangeOrderStatus(this.refs.orderStatus.value)
    }

    handleChangeViewers(selectedViewers) {
        let message = "Sharing with <br />"
        message += "<div class='dotted-hr' style='margin: 5px 0px'></div>"
        message += "<ul style='margin: 0px; margin-left: 10px; padding: 0px 10px'>"

        selectedViewers.forEach(viewer => {
            message += "<li>" + viewer.label + "</li>"
        })

        message += "</ul>"
        this.setState({
            viewersTooltip : message
        })

        $('.with-tooltip').tooltip();
        this.props.onChangeViewers(selectedViewers)
    }

    handleChangleEmailReceivers(selectedEmailReceivers) {
        this.props.onChangeEmailReceivers(selectedEmailReceivers)
    }

    render() {
        let styleObject = undefined
        if (this.props.isAssigneesVisible) {
            styleObject = {
                borderRadius: 0
            }
        }

        let privacySelect = null
        if (this.props.attachments.length > 0) {
            privacySelect = (
                <select id={'tooltip-on-show-' + this.props.subTaskDetailId} 
                        className="form-control form-control-sm" ref="privacy"
                        onChange={this.handleChangePrivacy}
                        data-toggle="tooltip" data-placement="bottom" 
                        title="Control who can see your attachments">
                    <option value="N">Normal</option>
                    <option value="P">Private</option>
                </select>
            )
        }

        return (
            <div className="text-left input-actions clearfix relative" style={styleObject}>
                <span >
                    <input ref="attachments" type="file" 
                        onChange={this.handleAddFile}
                        className="hidden" 
                        multiple />

                    <span className="with-tooltip"
                            data-toggle="tooltip"
                            data-placement="left"
                            data-html="true"
                            data-original-title={this.state.viewersTooltip}>

                        <i className="fa fa-key clickable" 
                           data-toggle="modal" 
                           data-target={"#modal-privacy-section-" + this.props.subTaskDetailId + (this.props.commentId !== undefined ? this.props.commentId : '') }>

                        </i> &nbsp;
                    </span>

                    <i className="fa fa-file-zip-o clickable with-tooltip" 
                       onClick={this.handleShowAddFile}
                       data-toggle="tooltip" 
                       data-placement="right"
                       title="Attach Files">
                    </i> 

                    &nbsp;&nbsp;
                </span>

                {privacySelect}

                <input type="submit" className="btn btn-sm btn-primary clickable float-right" value="Submit" style={{marginLeft: 10}}></input>
                <select className="form-control form-control-sm float-right" ref="orderStatus"
                        onChange={this.handleChangeOrderStatus}>
                    <option value="N">No Status</option>
                    {this.props.orderStatuses.map(orderStatus =>
                        <option key={orderStatus.id} value={orderStatus.id}>{orderStatus.status}</option>
                    )}
                </select>

                <PrivacySection viewers={this.props.viewers}
                    selectedViewers={this.props.selectedViewers} 
                    selectedEmailReceivers={this.props.selectedEmailReceivers}
                    onChangeViewers={this.handleChangeViewers} 
                    onChangeEmailReceivers={this.handleChangleEmailReceivers}
                    subTaskDetailId={this.props.subTaskDetailId}
                    commentId={this.props.commentId} />
            </div>
        )
    }
}