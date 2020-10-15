import React from "react";
import axios from "axios";
import InputSection from './inputSection'
import Reply from './reply'
import PrivacySection from './privacySection';

export default class Comment extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            isInputSectionVisible : false,
            isReplySectionVisible : false,
            isInitialLoadingVisible : true,
            isProductOrderCommentsVisible : false,
            productOrderCommentReplies : [],
            currentPage : 0,
            lastPage : 0,
            total : 0,

            viewers : [],
            selectedViewers : [],
            selectedAttachmentViewers : [],
            selectedEmailReceivers : [],

            selectedEditedViewers : [],
            selectedEditedEmailReceivers : [],
            doForAllComments : false,
            doForAllReplies : false,

            isSavingPrivacySettings : false,

            editPrivacySectionHasError : false,
            editPrivacySectionError : undefined,
        }

        this.handleAddComment = this.handleAddComment.bind(this)
        this.handleToggleInputSection = this.handleToggleInputSection.bind(this)
        this.handleToggleReplySection = this.handleToggleReplySection.bind(this)
        this.handleLoadMoreReplies = this.handleLoadMoreReplies.bind(this)
        this.handleChangeEditedViewers = this.handleChangeEditedViewers.bind(this)
        this.handleChangeEditedEmailReceivers = this.handleChangeEditedEmailReceivers.bind(this)
        this.handleSavePrivacySettings = this.handleSavePrivacySettings.bind(this)

        this.handleLoadViewers = this.handleLoadViewers.bind(this)
        this.handleChangeViewers = this.handleChangeViewers.bind(this)
        this.handleChangeEmailReceivers = this.handleChangeEmailReceivers.bind(this)
        this.handleChangeAttachmentViewers = this.handleChangeAttachmentViewers.bind(this)

        this.handleChangeDoForAllComments = this.handleChangeDoForAllComments.bind(this)
        this.handleChangeDoForAllReplies = this.handleChangeDoForAllReplies.bind(this)
    }

    componentDidMount() {
        $('.with-tooltip').tooltip();

        let selectedEditedViewers = []

        this.props.productOrderComment.viewers.forEach(viewer => {
            let department = viewer.department == null ? 
                'Multiple Department' :
                viewer.department.description

            selectedEditedViewers.push({
                'label' : viewer.first_name + ' ' + viewer.last_name + ' (' + department + ')',
                'value' : viewer.id 
            })
        })

        this.setState({
            selectedEditedViewers
        })
    }

    componentDidUpdate() {
        $('.with-tooltip').tooltip();
    }

    handleToggleInputSection() {
        this.setState({
            isInputSectionVisible : !this.state.isInputSectionVisible
        })
    }

    handleToggleReplySection() {
        this.setState({
            isReplySectionVisible : !this.state.isReplySectionVisible
        })
        
        if (this.state.total == 0 ) {
            this.loadReplies(1)
        }
    }

    handleAddComment(reply) {
        let productOrderCommentReplies = this.state.productOrderCommentReplies.slice()
        productOrderCommentReplies.unshift(reply)

        this.setState({
            productOrderCommentReplies : productOrderCommentReplies,
            total : this.state.total += 1
        })

        this.props.onAddRepliesCount(this.props.index)
    }

    handleLoadMoreReplies() {
        this.loadReplies(this.state.currentPage + 1)
    }

    loadReplies(i) {
        let commentUrl  = '/merchants/workflow/'
        commentUrl += this.props.orderId + '/'
        commentUrl += this.props.subTaskDetailId
        commentUrl += '/comments?page=' + i
        commentUrl += '&productOrderCommentId=' + this.props.productOrderComment.id

        axios.get(commentUrl)
            .then(response => {
                let productOrderCommentReplies  = undefined
                if (this.state.total == 0) {
                    productOrderCommentReplies = response.data.data
                } else {
                    productOrderCommentReplies = this.state.productOrderCommentReplies.slice()
                    productOrderCommentReplies = productOrderCommentReplies.concat(response.data.data)
                }

                this.setState({ 
                    isInitialLoadingVisible : false,
                    productOrderCommentReplies : productOrderCommentReplies,
                    currentPage : response.data.meta.current_page,
                    lastPage : response.data.meta.last_page,
                    total : response.data.meta.total
                })
            })
            .catch(error => {
                console.log(error)
            })
    }

    handleChangeEditedViewers(selectedEditedViewers) {
        this.setState({selectedEditedViewers})
    }

    handleChangeEditedEmailReceivers(selectedEditedEmailReceivers) {
        this.setState({selectedEditedEmailReceivers})
    }

    handleSavePrivacySettings() {
        if (this.state.selectedEditedViewers.length == 0) {
            this.setState({
                editPrivacySectionHasError : true,
                editPrivacySectionError : 'Please select users who can view this message'
            })

            return false
        }

        let formData = new FormData()
        formData.append('doForAllComments', this.state.doForAllComments)
        formData.append('doForAllReplies', this.state.doForAllReplies)

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

    handleLoadViewers() {
        let assigneesUrl  = '/merchants/workflow/'
        assigneesUrl += this.props.orderId + '/'
        assigneesUrl += this.props.subTaskDetailId
        assigneesUrl += '/comments/viewers'
        assigneesUrl += '?comment_id=' + this.props.productOrderComment.id

        this.setState({
            isLoadingVisible : true,
        })

        axios.get(assigneesUrl)
            .then(response => {

                let viewers = []
                let selectedViewers = []

                response.data.users.forEach(user => {
                    viewers.push({
                        'label' : user.name + ' (' + user.department + ')',
                        'value' : user.id
                    })               
                    
                    
                    if (user.viewer) {
                        selectedViewers.push({
                            'label' : user.name + ' (' + user.department + ')',
                            'value' : user.id 
                        })
                    }
                })

                this.setState({ 
                    viewers : viewers,
                    selectedViewers : selectedViewers,
                    selectedEmailReceivers : selectedViewers,
                    isLoadingVisible : false
                })
            })
            .catch(error => {
                console.log(error)
                this.setState({ 
                    isLoadingVisible : false
                })
            })
    }

    handleChangeViewers(selectedViewers) {
        let selectedAttachmentViewers = this.state.selectedAttachmentViewers
        let selectedEmailReceivers = this.state.selectedEmailReceivers

        this.setState({ 
            selectedViewers : selectedViewers,
            selectedAttachmentViewers : selectedAttachmentViewers.filter(value => -1 !== selectedViewers.indexOf(value)),
            selectedEmailReceivers: selectedEmailReceivers.filter(value => -1 !== selectedViewers.indexOf(value))
        });
    }

    handleChangeEmailReceivers(selectedEmailReceivers) {
        this.setState({ selectedEmailReceivers });
    }

    handleChangeAttachmentViewers(selectedAttachmentViewers) {
        this.setState({ selectedAttachmentViewers });
    }

    handleChangeDoForAllComments(checked) {
        this.setState({doForAllComments : checked})
    }

    handleChangeDoForAllReplies(checked) {
        this.setState({doForAllReplies : checked})
    }

    render() {
        let user = this.props.productOrderComment.user
        let className = this.props.index % 2 == 0 ?
            'comment-box comment-box-even' : 'comment-box comment-box-odd'
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

        let productOrderCommentReplies = (
            <div className='comment-box reply-comment-box' style={{borderBottom: 0}}>
                No replies for this comment
            </div>
        )

        if (this.state.productOrderCommentReplies != 0) {
            productOrderCommentReplies = this.state.productOrderCommentReplies.map((productOrderCommentReply, i) => 
                <Reply key={productOrderCommentReply.id}
                    index={i}
                    productOrderComment={productOrderCommentReply} />
            )
        } else {
            if (this.state.isInitialLoadingVisible) {
                productOrderCommentReplies = (
                    <div className='comment-box reply-comment-box' style={{borderBottom: 0, padding: 20}}>
                        <div className="overlay-no-bg">
                            <div id="text">
                                <img width="20px"
                                    height="20px"
                                    src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
                            </div>
                        </div>
                    </div>
                )
            }
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
            <div className={className} style={this.props.isLatest !== undefined ? {borderRadius:10} : {}}>
                <p>
                    <img src={this.props.productOrderComment.user_image} style={{ width : '25px', height : '25px', borderRadius : '100%', border : '1px solid black', marginRight : '8px'  }} />
                    <span className="comment-author">   
                        <b>{user}</b>
                    </span>
                    
                    <span className="float-right">
                        <span className="comment-time">
                            {this.props.productOrderComment.created_at} &nbsp;
                            <i className="fa fa-info-circle with-tooltip" 
                               data-toggle="tooltip"
                               data-placement="top"
                               data-html="true"
                               data-original-title={message}>
                            </i> 

                            {!this.props.isLatest && this.props.productOrderComment.owner  ? 
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

                <p>{this.props.productOrderComment.comment}</p>

                {attachments}

                <p className="reply-buttons col-sm-12 text-center">
                    <span className="clickable" onClick={this.handleToggleInputSection}>
                        <i className="fa fa-reply"></i>&nbsp;Reply
                    </span>&nbsp;&nbsp;&nbsp;

                    <span className="clickable" onClick={this.handleToggleReplySection}>
                        <i className="fa fa-comment"></i>&nbsp;
                        {this.state.isReplySectionVisible ? 
                            'Hide Replies (' + this.props.productOrderComment.replies_count + ')'  : 
                            'Show Replies (' + this.props.productOrderComment.replies_count + ')' }
                    </span>
                </p>

                <div className="col-sm-11 offset-sm-1 px-0">
                    {this.state.isInputSectionVisible ?
                        <InputSection orderId={this.props.orderId}
                            orderStatuses={this.props.orderStatuses}
                            subTaskDetailId={this.props.subTaskDetailId}
                            onAddComment={this.handleAddComment}
                            isReply={true}
                            commentId={this.props.productOrderComment.id}
                            
                            onLoadViewers={this.handleLoadViewers}
                            onChangeViewers={this.handleChangeViewers}
                            onChangeEmailReceivers={this.handleChangeEmailReceivers}
                            onChangeAttachementViewers={this.handleChangeAttachmentViewers}
                            
                            viewers={this.state.viewers}
                            selectedViewers={this.state.selectedViewers}
                            selectedEmailReceivers={this.state.selectedEmailReceivers}
                            selectedAttachmentViewers={this.state.selectedAttachmentViewers} /> :

                        undefined
                    }

                    {this.state.isReplySectionVisible ?
                        productOrderCommentReplies :
                        undefined
                    }

                    {this.state.isReplySectionVisible && this.state.currentPage != this.state.lastPage ?
                        <span className="reply-load-more clickable" 
                              onClick={this.handleLoadMoreReplies}>
                            <i>Load more ({this.props.productOrderComment.replies_count - this.state.productOrderCommentReplies.length} replies still hidden)</i>
                        </span> :
                        undefined
                    }

                    {this.props.productOrderComment.owner ? 
                        <PrivacySection subTaskDetailId={this.props.subTaskDetailId}
                            commentId={this.props.productOrderComment.id}
                        
                            viewers={this.props.viewers}
                            selectedViewers={this.state.selectedEditedViewers} 
                            selectedEmailReceivers={this.state.selectedEditedViewers}

                            onChangeEditedViewers={this.handleChangeEditedViewers} 
                            onChangeEditedEmailReceivers={this.handleChangeEditedEmailReceivers}
                            onSavePrivacySettings={this.handleSavePrivacySettings}

                            onChangeDoForAllComments={this.handleChangeDoForAllComments}
                            onChangeDoForAllReplies={this.handleChangeDoForAllReplies}

                            editPrivacySectionHasError={this.state.editPrivacySectionHasError}
                            editPrivacySectionError={this.state.editPrivacySectionError}

                            isSavingPrivacySettings={this.state.isSavingPrivacySettings}

                            withButton={true} /> : 
                        undefined
                    }
                    
                </div>
            </div>
        )
    }
}