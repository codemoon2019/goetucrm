import React from "react";
import axios from "axios";
import swal from "sweetalert2";
import Select from 'react-select';
import InputAttachments from './inputAttachments';
import InputActions from './inputActions';
import 'react-select/dist/react-select.css'


export default class InputSection extends React.Component {
    constructor(props) {
        super(props)

        this.state = {
            isLoadingVisible : false,
            isAssigneesVisible : false,

            isErrorsVisible : false,
            errorMessage : undefined,

            attachments : [],
            orderStatus : 'N',
        }


        this.handleAddFile = this.handleAddFile.bind(this)
        this.handleRemoveFile = this.handleRemoveFile.bind(this)
    
        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleChangePrivacy = this.handleChangePrivacy.bind(this)
        this.handleChangeOrderStatus = this.handleChangeOrderStatus.bind(this)

        this.handleSelectChange = this.handleSelectChange.bind(this)
        this.handleChangeViewers = this.handleChangeViewers.bind(this)
        this.handleChangeEmailReceivers = this.handleChangeEmailReceivers.bind(this)
    }

    componentDidMount() {
        if (this.props.viewers.length == 0 ) {
            this.props.onLoadViewers()
        }
    }

    handleAddFile(e) {
        let fileArray =[]
        let files = e.target.files
        let hasError = false

        Array.from(files).forEach(file => {
            if (file.size > 5242880) {
                hasError = true
            } else {
                fileArray = fileArray.concat([file])
            }
        });

        let updatedAttachments = this.state.attachments.slice()
            updatedAttachments = updatedAttachments.concat(fileArray)
            
        this.setState({ 
            attachments : updatedAttachments 
        })

        if (hasError) {
            swal({
                type: 'error',
                title: 'Maximum File Size Exceeded',
                text: 'File may not be greated than 5mb',
                animation: true,
                showConfirmButton: true,
                allowOutsideClick: false,
                position: "center"
            })
        }
    }

    handleRemoveFile(key) {
        let updatedAttachments = this.state.attachments
        updatedAttachments.splice(key, 1)

        if (updatedAttachments.length == 0) {
            this.setState({
                isAssigneesVisible : false,
                attachments : updatedAttachments
            })
        } else {
            this.setState({
                attachments : updatedAttachments
            })
        }
        
    }
    
    handleSubmit(e) {
        e.preventDefault()

        if (this.refs.comment.value.trim().length == 0) {
            this.setState({
                isErrorsVisible : true,
                errorMessage : 'Comment content is required'
            })

            return false
        }

        if (this.props.selectedViewers.length == 0) {
            this.setState({
                isErrorsVisible : true,
                errorMessage : 'Please select users who can view this message'
            })

            return false
        }

        this.setState({
            isLoadingVisible : true,
            isErrorsVisible : false
        })

        let formData = new FormData()
        formData.append('comment', this.refs.comment.value)
        formData.append('order_status', this.state.orderStatus)

        let viewers = this.props.selectedViewers
        viewers.forEach(viewer => {
            formData.append('viewers[]', viewer.value)
        })

        let attachments = this.state.attachments
        attachments.forEach(attachment => {
            formData.append('attachments[]', attachment, attachment.name)
        })

        if (this.state.isAssigneesVisible) {
            let selectedAttachmentViewers = this.props.selectedAttachmentViewers
            selectedAttachmentViewers.forEach(attachmentViewer => {
                formData.append('attachmentViewers[]', attachmentViewer.value)
            })
        }

        if (this.props.isReply) {
            formData.append('parent_id', this.props.commentId)
        }

        let commentUrl  = '/merchants/workflow/'
        commentUrl += this.props.orderId + '/'
        commentUrl += this.props.subTaskDetailId
        commentUrl += '/comments'

        axios.post(commentUrl, formData, {
                headers: { 
                    'content-type': 'multipart/form-data',
                    'X-Requested-With' : 'XMLHttpRequest'
                } 
            })
            .then(response => {

                if (this.props.isReply) {
                    this.props.onAddComment(response.data)
                } else {
                    this.props.onAddComment()
                }

                this.refs.comment.value = ''
                this.setState({
                    attachments : [],
                    isLoadingVisible: false,
                    isAssigneesVisible: false,
                })
            })
            .catch(error => {
                /** @todo Handle errors */
                console.log(error.response)

                this.setState({
                    isLoadingVisible: false
                })
            })
    }

    handleSelectChange(selectedAttachmentViewers) {
        this.props.onChangeAttachmentViewers(selectedAttachmentViewers)
    }

    handleChangePrivacy(value) {
        if (value == 'P') {
            this.setState({ isAssigneesVisible : true })
        } else {
            this.setState({ isAssigneesVisible : false })
        }
    }

    handleChangeOrderStatus(value) {
        this.setState({ orderStatus : value })
    }

    handleChangeViewers(selectedViewers) {
        this.props.onChangeViewers(selectedViewers)
    }

    handleChangeEmailReceivers(selectedEmailReceivers) {
        this.props.onChangeEmailReceivers(selectedEmailReceivers)
    }

    render() {
        let className = this.state.isLoadingVisible ? 'overlay' : 'overlay hidden'
        let inputAttachments = undefined
        if (this.state.attachments.length != 0) {
            inputAttachments = <InputAttachments attachments={this.state.attachments} 
                                                onRemoveFile={this.handleRemoveFile} />
        }
        

        let assignees = undefined
        if (this.state.isAssigneesVisible) {
            assignees = (
                <div className="text-left input-assignees">
                    <Select multi
                        placeholder="Select assignees that can view attachments"
                        options={this.props.selectedViewers}
                        value={this.props.selectedAttachmentViewers}
                        className="basic-multi-select"
                        classNamePrefix="select"
                        onChange={this.handleSelectChange}
                        key={this.props.subTaskDetailId} />
                </div>
            )
        }

        let boxClassName = 'col-sm-10 offset-sm-1'
        let secondaryBoxClassName = 'text-right input-section relative'
        let rows = 4
        if (this.props.isReply) {
            rows = 2
            boxClassName = 'px-0 ml-0'  
            secondaryBoxClassName = 'text-right input-section reply-input-section relative'
        }
        
        return (
            <div className={boxClassName}>
                <div className={secondaryBoxClassName}>
                    <div className={className}>
                        <div id="text">
                            <img width="35px"   
                                height="35px"
                                src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
                        </div>
                    </div>

                    <form action="/merchants/workflow/comment" onSubmit={this.handleSubmit}>
                        <textarea ref="comment" 
                            className="form-control" 
                            placeholder="Type text here..."
                            rows={rows}></textarea>

                        {inputAttachments}

                        <InputActions isAssigneesVisible={this.state.isAssigneesVisible}
                            subTaskDetailId={this.props.subTaskDetailId}
                            commentId={this.props.commentId}
                            orderStatuses={this.props.orderStatuses}

                            attachments={this.state.attachments}
                            onAddFile={this.handleAddFile}
                            onChangePrivacy={this.handleChangePrivacy}

                            viewers={this.props.viewers}
                            selectedViewers={this.props.selectedViewers}
                            selectedEmailReceivers={this.props.selectedEmailReceivers}
                            onChangeViewers={this.handleChangeViewers}
                            onChangeEmailReceivers={this.handleChangeEmailReceivers}

                            onChangeOrderStatus={this.handleChangeOrderStatus} />

                        {assignees}
                    </form>
                </div>

                {this.state.isErrorsVisible ? 
                    <div className="input-section mx-0 py-0 pt-2" style={{border: 0, paddingTop: 10, color: 'red'}}>
                        <p>{this.state.errorMessage}</p>
                    </div> :

                    undefined
                }
                
            </div>
        )
    }
}





