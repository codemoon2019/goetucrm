import React from "react"
import InputAttachment from './inputAttachment'

export default class InputAttachments extends React.Component {
    constructor(props) {
        super(props)

        this.handleRemoveFile = this.handleRemoveFile.bind(this)
    }

    handleRemoveFile(key) {
        this.props.onRemoveFile(key)
    }

    render() {
        return (
            <div className="text-left attachments-box">
                <ul className="attachments-list" style={{marginBottom: 0}}>
                    {this.props.attachments.map((attachment, i) => 
                        <InputAttachment key={i} 
                            attachmentId={i}
                            attachment={attachment}
                            onRemoveFile={this.handleRemoveFile} />
                    )}
                </ul>
            </div>
        )
    }
}