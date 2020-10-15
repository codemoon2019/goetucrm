/**
 * Created by Jianfeng Li on 2017/5/17.
 */

import React from 'react';
import PropTypes from "prop-types";

const LoadingComponent = ({show, size}) => {

    if (show) {
        return (
            <div className="overlay">
                <i className={`fa fa-refresh ${ size === "" ? "" : ("fa-".size) } fa-spin`}/>
            </div>
        );
    } else {
        return null;
    }
};

LoadingComponent.propTypes = {
    show: PropTypes.bool,
    size: PropTypes.oneOf(["lg", "2x", "3x", "4x", "5x"]),
};

LoadingComponent.defaultProps = {
    show: false,
    size: "",
};

export default LoadingComponent;