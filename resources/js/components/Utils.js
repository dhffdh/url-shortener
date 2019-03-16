import React, { Component } from 'react';


/**
 *
 * @param props
 * @returns {*}
 * @constructor
 */
function RenderErrors(props){
    const { errors } = props;
    if(!errors.length)
        return null;
    return (
        <div className="alert alert-danger" role="alert">
            {
                errors.map( (mes,index) => <div key={index}>{ mes }</div>)
            }
        </div>
    )
}

export { RenderErrors };