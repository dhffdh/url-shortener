import React, { Component } from 'react';
import axios from "axios";


export default class Form extends Component {

    constructor(props){
        super();
        this.state = {
            href: "",
            errorList: []
        };
        this.submitHandler = this.submitHandler.bind(this);
        this.validate = this.validate.bind(this);
    }

    submitHandler(e){
        e.preventDefault();

        //console.log('submitHandler', this.state );

        axios.post('/urls', { href: this.state.href })
            .then(
                res => {
                    //console.log('submitHandler res:',res.data);
                    this.setState({
                        href: "",
                        errorList: []
                    });
                    if(this.props.onSuccesAdd){
                        this.props.onSuccesAdd(res.data);
                    }
                }
            )
            .catch(error => {
                //console.log('ERROR', errors );
                this.setState({
                    errorList: Object.values(error.response.data.errors)
                })
            })
    };

    handleInput(e) {
        this.setState({
            href: e.target.value
        });
    }


    renderErrors(){
        const { errorList } = this.state;
        return errorList.length > 0 ?
            (
                <div className="alert alert-danger" role="alert">
                    {
                        errorList.map( (mes,index) => <div key={index}>{ mes }</div>)
                    }
                </div>
            ) : null
    }

    validate(){
        if(this.state.href.length>0)
            return true;


        return false;
    }

    render() {
        return (
            <div className="card">
                <div className="card-header">URL Shortener</div>
                <div className="card-body">
                    <p>This tool will help you turn a long and complicated link into a short one.</p>
                    <form onSubmit={this.submitHandler}>
                        <div className="form-group">
                            <input type="text"
                                   className="form-control"
                                   placeholder="Enter long URL-link here"
                                   onChange={(e)=>this.handleInput(e)}
                                   value={this.state.href}
                            />
                        </div>
                        {/*<div className="form-group form-check">
                            <input type="checkbox" className="form-check-input" id="exampleCheck1"/>
                            <label className="form-check-label" for="exampleCheck1">Check me out</label>
                        </div>*/}

                        <div className="form-group">
                            <button type="submit" className="btn btn-primary" disabled={!this.validate()}>Shorten</button>
                        </div>

                        {/*<div className="form-group">
                            <label>URL:</label>
                            <input type="text" className="form-control" placeholder=""/>
                        </div>*/}
                    </form>

                    {
                        this.renderErrors()
                    }

                </div>
            </div>

        )
    }
}