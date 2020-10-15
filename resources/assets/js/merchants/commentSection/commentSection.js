import React from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import InputSection from './inputSection'
import CommentArea from './commentArea'
import Comment from './comment'

import 'react-select/dist/react-select.css'

class CommentSection extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            isInitialLoading : true,
            isCommentSectionVisible : false,
            latestComment : undefined,
            productOrderComments : [],
            productOrderCommentsCount : 0,
            currentPage : 0,
            lastPage : 0,

            viewers : [],
            selectedViewers : [],
            selectedAttachmentViewers : [],
            selectedEmailReceivers : [],
        }

        this.handleToggleCommentSection = this.handleToggleCommentSection.bind(this)
        this.handleAddComment = this.handleAddComment.bind(this)
        this.handleLoadComments = this.handleLoadComments.bind(this)
        this.handleAddRepliesCount = this.handleAddRepliesCount.bind(this)
        this.handleChangePage = this.handleChangePage.bind(this)

        this.handleLoadViewers = this.handleLoadViewers.bind(this)
        this.handleChangeViewers = this.handleChangeViewers.bind(this)
        this.handleChangeEmailReceivers = this.handleChangeEmailReceivers.bind(this)
        this.handleChangeAttachmentViewers = this.handleChangeAttachmentViewers.bind(this)
    }

    componentDidMount() {
        this.handleLoadComments()
    }

    handleToggleCommentSection(e) {
        e.preventDefault()
        this.setState({ 
            isCommentSectionVisible : !this.state.isCommentSectionVisible   
        })
    }

    handleAddComment() {
        this.loadComments(this.state.currentPage)
    }

    handleLoadComments() {  
        if ( this.state.productOrderComments.length == 0 ) {
            this.loadComments(1)
        }
    }

    handleAddRepliesCount(index) {
        if (index == 'x') {
            let latestComment = this.state.latestComment
            latestComment.replies_count += 1

            this.setState({
                latestComment : latestComment
            })
        } else {
            let productOrderComments = this.state.productOrderComments
            productOrderComments[index].replies_count += 1 
            
            this.setState({
                productOrderComments : productOrderComments
            })
        }
    }

    handleChangePage(i) {
        this.setState({
            isInitialLoading : true,
        })
        
        this.loadComments(i)
    }

    handleLoadViewers() {
        let assigneesUrl  = '/merchants/workflow/'
        assigneesUrl += this.props.orderId + '/'
        assigneesUrl += this.props.subTaskDetailId
        assigneesUrl += '/comments/viewers'
        assigneesUrl += this.props.isReply ? '?comment_id=' + this.props.commentId : ''

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

    loadComments(i) {
        let commentUrl  = '/merchants/workflow/'
            commentUrl += this.props.orderId + '/'
            commentUrl += this.props.subTaskDetailId
            commentUrl += '/comments?page=' + i

            axios.get(commentUrl)
                .then(response => {
                    let productOrderComments = response.data.data   

                    this.setState({
                        productOrderComments : productOrderComments,
                        currentPage : response.data.meta.current_page,
                        lastPage : response.data.meta.last_page,
                        isInitialLoading : false,
                        productOrderCommentsCount : response.data.meta.total,
                    })

                    if (response.data.meta.current_page == 1) {
                        this.setState({
                            latestComment : this.state.productOrderComments[0]
                        })
                    } 
                })
                .catch(error => {
                    this.setState({ 
                        isInitialLoading : false,
                    })
                })
    }

    render() {
        let inputSection = undefined;
        let commentSection = undefined;

        if (this.state.isCommentSectionVisible) {
            inputSection = (
                <InputSection orderId={this.props.orderId}
                    orderStatuses={this.props.orderStatuses}
                    subTaskDetailId={this.props.subTaskDetailId}
                    onAddComment={this.handleAddComment}
                    
                    onLoadViewers={this.handleLoadViewers}
                    onChangeViewers={this.handleChangeViewers}
                    onChangeEmailReceivers={this.handleChangeEmailReceivers}
                    onChangeAttachementViewers={this.handleChangeAttachmentViewers}
                    
                    viewers={this.state.viewers}
                    selectedViewers={this.state.selectedViewers}
                    selectedEmailReceivers={this.state.selectedEmailReceivers}
                    selectedAttachmentViewers={this.state.selectedAttachmentViewers}

                    isReply={false} />
            )

            commentSection = (
                <CommentArea orderId={this.props.orderId}
                    orderStatuses={this.props.orderStatuses}
                    subTaskDetailId={this.props.subTaskDetailId}
                    productOrderComments={this.state.productOrderComments}
                    isInitialLoading={this.state.isInitialLoading}
                    onLoadComments={this.handleLoadComments}
                    onAddRepliesCount={this.handleAddRepliesCount}
                    currentPage={this.state.currentPage}
                    lastPage={this.state.lastPage}
                    onChangePage={this.handleChangePage}

                    viewers={this.state.viewers}
                    selectedViewers={this.state.selectedViewers}
                    selectedEmailReceivers={this.state.selectedEmailReceivers}
                    selectedAttachmentViewers={this.state.selectedAttachmentViewers} />
            )
        } else {
            if (this.state.latestComment !== undefined) {
                commentSection = (
                    <div className='col-sm-10 offset-sm-1'>
                        <h5 style={{marginBottom:20, borderBottom: '1px solid rgba(0,0,0,0.1)' }}><i>Latest Comment </i></h5>
                        <div className="comment-area relative">
                            <Comment key={this.state.latestComment.id}
                                index={'x'}
                                productOrderComment={this.state.latestComment}
                                orderId={this.props.orderId}
                                orderStatuses={this.props.orderStatuses}
                                subTaskDetailId={this.props.subTaskDetailId}
                                onAddRepliesCount={this.handleAddRepliesCount}
                                isLatest={true} />
                        </div>
                    </div>
                )
            } else {
                if (this.state.isInitialLoading) {
                    commentSection = (
                        <div className='col-sm-10 offset-sm-1'>
                            <h5 style={{marginBottom:20, borderBottom: '1px solid rgba(0,0,0,0.1)' }}><i>Latest Comment </i></h5>
                            <div className="comment-area relative">
                                <div className="overlay-no-bg">
                                    <div id="text">
                                        <img width="50px"
                                            height="50px" 
                                            src="https://ubisafe.org/images/transparent-gif-loading-5.gif" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    )
                }
            }
        }

        return (
            <div>
                <div className="col-sm-12 text-center">
                    <div className="col-sm-10 offset-sm-1 px-0">
                        <div className="dotted-hr"></div>
                    </div>

                    <p>
                        <span className="clickable" onClick={this.handleToggleCommentSection}>
                            <i className="fa fa-comment-o"></i> 
                            <span>&nbsp;Comment ({this.state.isInitialLoading ?  'Loading...' : this.state.productOrderCommentsCount})</span>
                        </span>
                    </p>
                </div>

                <div className="row">
                    <div className="col-sm-12">
                        {inputSection}

                        {this.state.isCommentSectionVisible ? <br /> : undefined}

                        {commentSection}
                    </div>
                </div>
            </div>
        )
    }
}


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let commentSections = document.getElementsByClassName('comment-section')
for (var i = 0; i < commentSections.length; i++) {
    ReactDOM.render(<CommentSection 
        orderId={commentSections[i].dataset.order_id}
        orderStatuses={JSON.parse(commentSections[i].dataset.order_statuses)}
        subTaskDetailId={commentSections[i].dataset.sub_task_detail_id}
    />, commentSections[i])
}