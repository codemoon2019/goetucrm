import React from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import htmlEntitites from "html-entities";
import swal from "sweetalert2";

class ReplySection extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            currentPage : 0,
            lastPage : 0,
            replies : []
        }

        this.showMore = this.showMore.bind(this)
        this.showLess = this.showLess.bind(this)
    }

    componentDidMount() {
        
        let id = document.getElementById('server-data-th-id').value

        axios.get('/tickets/ticket-details?ticket_id=' + id)
            .then(response => {
                const currentPage = response.data.meta.current_page
                const replies = response.data.data
                const lastPage = response.data.meta.last_page

                this.setState({ 
                    currentPage : parseInt(currentPage),
                    replies : replies,
                    lastPage : parseInt(lastPage)
                })
            })
            .catch(error => console.log(error))
    }

    showMore(e) {
        e.preventDefault()
        let id = document.getElementById('server-data-th-id').value
        let page = Number(this.state.currentPage) + 1

        swal({
            title: 'Loading Replies...',
            text: 'Please wait while loading replies.',
            imageUrl: document.querySelector("#ctx").getAttribute("content") + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        })

        axios.get('/tickets/ticket-details?ticket_id=' + id + '&page=' + page)
            .then(response => {
                let currentPage = response.data.meta.current_page
                let lastPage = response.data.meta.last_page

                let updatedReplies = this.state.replies.slice()
                    updatedReplies = updatedReplies.concat(response.data.data)

                this.setState({ 
                    currentPage : currentPage,
                    replies : updatedReplies,
                    lastPage : lastPage
                })

                swal.close()
            })
            .catch(error => console.log(error))
    }

    showLess(e) {
        e.preventDefault()

        this.setState({
            currentPage : 1,
            replies : this.state.replies.slice(0, 4),
        })
    }

    render() {
        let buttonShowMore = undefined
        if (this.state.currentPage != this.state.lastPage) {
            buttonShowMore = (
                <button className="btn btn-primary"
                        onClick={this.showMore}
                        style={{cursor : 'pointer'}}>Show More</button>
            )
        }

        let buttonShowLess = undefined
        if (this.state.currentPage != 1) {
            buttonShowLess = (
                <button className="btn btn-danger"
                        onClick={this.showLess}
                        style={{cursor : 'pointer'}}>Show Less</button>
            )
        }

        return (
            <div>
                {this.state.replies.map(reply => <Reply key={reply.id} reply={reply} />)}

                {!(buttonShowMore === undefined && buttonShowLess === undefined) && 
                    <div className="text-center">
                        <br />
                        <div className="col-12">
                            {buttonShowMore} {buttonShowLess}
                        </div>
                        <br />
                    </div>
                }
            </div>
        )
    }
}

class Reply extends React.Component {
    constructor(props) {
        super(props)
    }

    isHidden(value) {
        let isInternalUser = document.getElementById('server-data-internal').value
        let isInternal = value

        return isInternalUser == 0 && isInternal == 1 ?
            'col-md-12 px-0 hidden' :
            'col-md-12 px-0'
    }

    render() {
        let htmlEntitiesObject = new htmlEntitites.AllHtmlEntities;
        let attachments = undefined
        if (this.props.reply.attachments.length != 0) { 
            attachments = (
                <div className="row">
                    {this.props.reply.attachments.map(attachment => <Attachment key={attachment.id} attachment={attachment} />)}
                </div>            
            )
        }
        
        let backgroundColor = { borderBottom: '1.5px solid black' }
        if (this.props.reply.is_internal == 1) {
            backgroundColor = {
                borderBottom : '1.5px solid black',
                backgroundColor : 'lightgray'
            }
        }

        return (
            <div className={ this.isHidden(this.props.reply.is_internal) }>
                <div className="msg-reply" style={backgroundColor}>
                    <div className="msg-user-pic">
                        <img src={this.props.reply.image} />
                    </div>

                    <div className="msg-content">
                        <p className="msg-head">
                            <span>
                                { this.props.reply.created_by.first_name + ' ' + this.props.reply.created_by.last_name }
                            </span>
                            <span className="time-ago">{this.props.reply.created_at}</span>
                        </p>

                        <div dangerouslySetInnerHTML={{ __html: htmlEntitiesObject.decode( this.props.reply.message ) }}></div>
                        <div className="clearfix"></div>

                        {attachments}
                    </div>
                </div>
            </div>
        )
    }
}

class Attachment extends React.Component {
    render() {
        let baseUrl = document.getElementById('server-data-path').value

        return (
            <div className="col-sm-3 text-center">
                <i className="fa fa-file fa-1x text-center"></i>
                <a href={baseUrl + this.props.attachment.path} target="_blank">&nbsp;{this.props.attachment.name}</a>
            </div> 
        )
    }
}

ReactDOM.render(<ReplySection />, document.getElementById('reply-section'))