import React from "react"

export default class InputAttachment extends React.Component {
    constructor(props) {
        super(props)

        this.handleRemoveFile = this.handleRemoveFile.bind(this)
    }

    handleRemoveFile() {
        this.props.onRemoveFile(this.props.attachmentId)
    }

    render() {
        return (
            <li>
                <span className="attachment-icon">
                    <i className="fa fa-file"></i>
                </span>
                
                <span className="attachment-name">
                    {this.props.attachment.name}
                </span>
                
                <span className="attachment-remove float-right clickable" onClick={this.handleRemoveFile}>
                    <i className="fa fa-remove"></i>
                </span>
            </li>
        )
    }
}