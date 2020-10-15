import React from "react";
import Select from 'react-select';

export default class PrivacySection extends React.Component {
    constructor(props) {    
        super(props)

        this.handleChangeViewers = this.handleChangeViewers.bind(this)
        this.handleChangleEmailReceivers = this.handleChangleEmailReceivers.bind(this)
        this.handleSavePrivacySettings = this.handleSavePrivacySettings.bind(this)
        this.handleChangeDoForAllComments = this.handleChangeDoForAllComments.bind(this)
        this.handleChangeDoForAllReplies = this.handleChangeDoForAllReplies.bind(this)
    }

    handleChangeViewers(selectedViewers) {
        if (this.props.onChangeViewers === undefined) {
            this.props.onChangeEditedViewers(selectedViewers)
        } else {
            this.props.onChangeViewers(selectedViewers)
        }
    }
 
    handleChangleEmailReceivers(selectedEmailReceivers) {
        if (this.props.onChangeEmailReceivers === undefined) {
            this.props.onChangeEditedEmailReceivers(selectedEmailReceivers)
        } else {
            this.props.onChangeEmailReceivers(selectedEmailReceivers)
        }
    }

    handleSavePrivacySettings() {
        this.props.onSavePrivacySettings()
    }

    handleChangeDoForAllComments() {
        this.props.onChangeDoForAllComments(this.refs.do_for_all_comment.checked)
    }

    handleChangeDoForAllReplies() {
        this.props.onChangeDoForAllReplies(this.refs.do_for_all_replies.checked)
    }

    render() {
        let id = "modal-privacy-section-" + this.props.subTaskDetailId
        id += this.props.withButton !== undefined ? '-e-' : ''
        id += this.props.commentId !== undefined ? this.props.commentId : ''

        return (
            <div id={id} className="modal" tabIndex="-1" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">
                                <strong>Control Privacy Settings of your Post</strong>
                            </h5>
                            <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div className="modal-body">
                            <p><b>Share with</b></p>
                            <div className="row">
                                <div className="col-sm-11 offset-sm-1">
                                    <div className="text-left">
                                        <br />
                                        <Select multi
                                            options={this.props.viewers}
                                            value={this.props.selectedViewers}
                                            placeholder="Select assignees that can view comment"
                                            className="basic-multi-select"
                                            classNamePrefix="select"
                                            key={this.props.subTaskDetailId} 
                                            onChange={this.handleChangeViewers} />
                                        <br />
                                    </div>
                                </div>
                            </div>

                            <div className="hidden">
                                <hr />

                                <p><b>Will receive Notifications</b></p>
                                <div className="row">
                                    <div className="col-sm-11 offset-sm-1">
                                        <div className="text-left">
                                            <br />
                                            <Select multi
                                                options={this.props.selectedViewers}
                                                value={this.props.selectedEmailReceivers}
                                                placeholder="Select assignees that can will receive notifications"
                                                className="basic-multi-select"
                                                classNamePrefix="select"
                                                key={this.props.subTaskDetailId} 
                                                onChange={this.handleChangleEmailReceivers} />
                                            <br />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {this.props.withButton !== undefined ? 
                                <div>
                                    {this.props.isReply === undefined ? 
                                        <div className="row">
                                            <div className="col-sm-12">
                                                <div className="form-check">
                                                    <input type="checkbox" 
                                                        className="form-check-input" 
                                                        ref="do_for_all_comment"
                                                        style={{marginLeft:0}}
                                                        onChange={this.handleChangeDoForAllComments} />

                                                    <label className="form-check-label" >
                                                        Do this for all my comments in this task
                                                    </label>
                                                </div>

                                                <div className="form-check">
                                                    <input type="checkbox" 
                                                        className="form-check-input" 
                                                        ref="do_for_all_replies" 
                                                        style={{marginLeft:0}}
                                                        onChange={this.handleChangeDoForAllReplies} />

                                                    <label className="form-check-label">
                                                        Do this for all my replies in this comment
                                                    </label>
                                                </div> 
                                            </div>
                                        </div> : 
                                        undefined
                                    }

                                    <hr />

                                    <div className="row">
                                        <div className="col-sm-11 offset-sm-1">
                                            <div className="text-left">
                                                <button className="btn btn-primary clickable" 
                                                        onClick={this.handleSavePrivacySettings}>
                                                    Save
                                                </button>

                                                &nbsp; &nbsp;

                                                {this.props.editPrivacySectionHasError == true ?
                                                    <span style={{color:'red'}}>
                                                        {this.props.editPrivacySectionError}
                                                    </span> :
                                                    undefined                                                
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div> :
                                undefined 
                            }
                        </div>
                    
                        {this.props.isSavingPrivacySettings ?
                            <div className="overlay">
                                <div id="text">
                                    <img width="35px"
                                        height="35px"
                                        src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
                                </div>
                            </div> : 
                            undefined
                        }
                        
                    </div>
                </div>
            </div>
        )
    }
}