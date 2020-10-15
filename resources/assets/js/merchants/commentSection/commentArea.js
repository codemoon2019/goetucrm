import React from "react";
import Comment from './comment'

export default class CommentArea extends React.Component {
    constructor(props) {
        super(props)
       
        this.handleAddRepliesCount = this.handleAddRepliesCount.bind(this)
    }

    componentDidMount() {
        this.props.onLoadComments() 
    }

    handleAddRepliesCount(index) {
        this.props.onAddRepliesCount(index)
    }

    handleChangePage(e, i) {
        e.preventDefault()
        
        this.props.onChangePage(i)
    }

    render() {
        let productOrderComments = (
            <div className="comment-box">
                No comments for this task
            </div>
        )

        let initialLoading = undefined
        if (this.props.isInitialLoading) {
            initialLoading = (
                <div className="overlay">
                    <div id="text">
                        <img width="50px"
                            height="50px" 
                            src="https://ubisafe.org/images/transparent-gif-loading-5.gif" />
                    </div>
                </div>
            )
        }

        if (this.props.productOrderComments.length != 0) {
            productOrderComments = this.props.productOrderComments.map((productOrderComment, i) => 
                <Comment key={productOrderComment.id}
                    index={i}
                    productOrderComment={productOrderComment}
                    orderId={this.props.orderId}
                    orderStatuses={this.props.orderStatuses}
                    subTaskDetailId={this.props.subTaskDetailId}
                    onAddRepliesCount={this.handleAddRepliesCount}

                    viewers={this.props.viewers} />
            )
        } else {
            if (this.props.isInitialLoading) {
                productOrderComments = undefined
                initialLoading = (
                    <div className="overlay-no-bg">
                        <div id="text">
                            <img width="50px"
                                height="50px" 
                                src="https://ubisafe.org/images/transparent-gif-loading-5.gif" />
                        </div>
                    </div>
                )
            }
        }

        return (
            <div className='col-sm-10 offset-sm-1'>
                <div className="comment-area relative">
                    {initialLoading}
                    {productOrderComments}
                </div>

                <nav aria-label="Page navigation example" style={{marginTop: 10}}>
                    <ul className="pagination justify-content-end" >
                        {[...Array(this.props.lastPage)].map((e, i) => 
                            <li className={"page-item " + ((i+1) == this.props.currentPage ? 'active' : '')} key={i}>
                                <a className="page-link"    
                                   href="#" 
                                   key={i} 
                                   onClick={(e) => this.handleChangePage(e, i+1)}>{i+1}</a>
                            </li>
                        )}
                    </ul>
                </nav>
            </div>
        )
    }
}

