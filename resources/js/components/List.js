import React, { Component } from 'react';


export default class List extends Component {

    constructor(props){
        super();
        this.onDeleteHandler = this.onDeleteHandler.bind(this);
    }

    onDeleteHandler(e,id){
        e.preventDefault();
        //console.log('onDeleteHandler',id);

        if(this.props.onDelete){
            this.props.onDelete(id);
        }
    }

    render() {
        let { urls } = this.props;
        urls = urls.reverse();
        return (
            <div className="card">
                <div className="card-header">Your shortened links</div>
                <ul className="list-group list-group-flush">
                    {
                        urls.map((url,index) => {
                            return <li className="list-group-item" key={index}>
                                <div className="d-flex justify-content-between">
                                    <div>
                                        <div className="font-weight-bolder"><a href={ url.short_href } target="_blank" >{ url.short_href }</a></div>
                                        <div className="font-italic">{ url.href }</div>
                                        {/*<div>Short code: { url.code }</div>*/}
                                        <div className="text-muted small">{ url.id }: { url.created_at }</div>
                                    </div>
                                    <div>
                                        <button type="button"
                                                className="close"
                                                onClick={ (e) => { this.onDeleteHandler(e,url.id) } }
                                                title="Delete"
                                        >
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        })
                    }
                </ul>
            </div>
        )
    }
}