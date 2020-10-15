import React from "react";
import PrivacySection from './privacySection';

export default class Reply extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            viewers : [],
            selectedEditedViewers : [],
            selectedEditedEmailReceivers : [],

            editPrivacySectionHasError : false,
            editPrivacySectionError : undefined,
        }

        this.handleChangeEditedViewers = this.handleChangeEditedViewers.bind(this)
        this.handleChangeEditedEmailReceivers = this.handleChangeEditedEmailReceivers.bind(this)
        this.handleSavePrivacySettings = this.handleSavePrivacySettings.bind(this)
    }

    componentDidMount() {
        $('.with-tooltip').tooltip();

        let viewers = []
        let selectedEditedViewers = []

        this.props.productOrderComment.viewers.forEach(viewer => {
            let department = viewer.department === undefined ? 
                'Multiple Department' :
                viewer.department.description

            viewers.push({
                'label' : viewer.first_name + ' ' + viewer.last_name + ' (' + department + ')',
                'value' : viewer.id 
            })

            selectedEditedViewers.push({
                'label' : viewer.first_name + ' ' + viewer.last_name + ' (' + department + ')',
                'value' : viewer.id 
            })
        })

        this.setState({
            viewers, 
            selectedEditedViewers
        })
    }

    componentDidUpdate() {
        $('.with-tooltip').tooltip();
    }

    handleChangeEditedViewers(selectedEditedViewers) {
        this.setState({selectedEditedViewers})
    }

    handleChangeEditedEmailReceivers(selectedEditedEmailReceivers) {
        this.setState({selectedEditedEmailReceivers})
    }

    handleSavePrivacySettings() {
        let formData = new FormData()
        let viewers = this.state.selectedEditedViewers
        viewers.forEach(viewer => {
        formData.append('viewers[]', viewer.value)
        })

        let syncViewersUrl  = '/merchants/workflow/'
        syncViewersUrl += this.props.orderId + '/'
        syncViewersUrl += this.props.subTaskDetailId
        syncViewersUrl += '/comments/'
        syncViewersUrl += this.props.productOrderComment.id

        this.setState({
            isSavingPrivacySettings : true
        })

        axios.post(syncViewersUrl, formData, {
                headers: { 
                    'content-type': 'multipart/form-data',
                    'X-Requested-With' : 'XMLHttpRequest'
                } 
            })
            .then(response => {
                this.setState({
                    isSavingPrivacySettings : false
                })
            })
            .catch(error => {
                /** @todo Handle errors */
                console.log(error.response)

                this.setState({
                    isSavingPrivacySettings : false
                })
            })
    }

    render() {
        let user = this.props.productOrderComment.user
        let attachments = undefined
        if (this.props.productOrderComment.attachments != null) {
            attachments = (
                <ul className='attachments-list'>
                    {this.props.productOrderComment.attachments.map((attachment) => 
                        <li key={attachment.id} >
                            <a href={attachment.path} download>
                                <span className="attachment-icon">
                                    <i className="fa fa-file"></i>
                                </span>

                                <span>{attachment.name}</span>
                            </a>
                        </li>
                    )}
                </ul>
            )
        }

        let message = "Shared with <br />"
        message += "<div class='dotted-hr' style='margin: 5px 0px'></div>"
        message += "<ul style='margin: 0px; margin-left: 10px; padding: 0px 10px'>"

        this.props.productOrderComment.viewers.forEach(viewer => {
            let department = viewer.department == null ? 'Multiple Department' : viewer.department.description

            message += "<li>"
            message += viewer.first_name + " " + viewer.last_name
            message += " (" + department + ")"
            message += "</li>"
        })

        message += "</ul>"

        let id = "modal-privacy-section-" + this.props.subTaskDetailId
        id += '-e-' + this.props.productOrderComment.id

        return (
            <div className='comment-box reply-comment-box'>
                <p>
                    <span className="comment-author">
                        <img src={this.props.productOrderComment.user_image} style={{ width : '20px', height : '20px', borderRadius : '100%', border : '1px solid black', marginRight : '5px' }} />
                        <b>{user}</b>
                    </span>
                    
                    <span className="float-right">
                        <span className="comment-time">
                            {this.props.productOrderComment.created_at} &nbsp;
                            <i className="fa fa-info-circle with-tooltip" 
                               data-toggle="tooltip"
                               data-placement="top"
                               data-html="true"
                               title={message}
                               data-original-title={message}>
                            </i>

                            {this.props.productOrderComment.owner ? 
                                <span className="with-tooltip" 
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    data-html="true"
                                    data-original-title="Edit Privacy Settings">

                                    &nbsp;
                                    
                                    <i className="fa fa-key clickable" 
                                        data-toggle="modal" 
                                        data-target={"#" + id}>
                                    </i>
                                </span> :

                                undefined
                            }
                        </span>
                    </span>
                </p>

                <p className="comment-content">{this.props.productOrderComment.comment}</p>

                {attachments}

                {this.props.productOrderComment.owner ? 
                    <PrivacySection subTaskDetailId={this.props.subTaskDetailId}
                        commentId={this.props.productOrderComment.id}

                        viewers={this.state.viewers}
                        selectedViewers={this.state.selectedEditedViewers} 
                        selectedEmailReceivers={this.state.selectedEditedViewers}
                        
                        onChangeEditedViewers={this.handleChangeEditedViewers} 
                        onChangeEditedEmailReceivers={this.handleChangeEditedEmailReceivers}
                        onSavePrivacySettings={this.handleSavePrivacySettings}

                        editPrivacySectionHasError={this.state.editPrivacySectionHasError}
                        editPrivacySectionError={this.state.editPrivacySectionError}
                        
                        withButton={true}
                        isReply={true} /> :

                    undefined
                }
            </div>
        )
    }
}