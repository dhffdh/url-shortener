import React, { Component } from 'react';
import axios from "axios";
import { RenderErrors } from "./Utils"


export default class Form extends Component {

    constructor(props){
        super();
        this.state = {
            href: "",
            code: "",
            errorList: [],
            isLoading: false,
            useCode: false,
        };
        this.submitHandler = this.submitHandler.bind(this);
        this.validate = this.validate.bind(this);
    }

    submitHandler(e){
        e.preventDefault();

        //console.log('submitHandler', this.state );
        this.setState({
            errorList: [],
            isLoading: true
        });


        axios.post('/urls', { href: this.state.href })
            .then(
                res => {
                    //console.log('submitHandler res:',res.data);
                    this.setState({
                        href: ""
                    });
                    if(this.props.onSuccesAdd){
                        this.props.onSuccesAdd(res.data);
                    }
                }
            )
            .catch(error => {
                //console.log('ERROR', errors );
                if(!!error.response.data.errors && error.response.data.errors.length){
                    this.setState({
                        errorList: Object.values(error.response.data.errors)
                    });
                }
                if(!!error.response.data.message){
                    this.setState({
                        errorList: [error.response.data.message]
                    });
                }
            })
            .finally(() => {
                this.setState({
                    isLoading: false
                });
            })
    };

    handleHrefInput(e) {
        this.setState({
            href: e.target.value
        });
    }

    handleCodeInput(e) {
        this.setState({
            code: e.target.value
        });
    }


    handleCheckbox(e) {
        //console.log('handleCheckbox', e.target.checked );
        this.setState({
            useCode: e.target.checked
        });
    }


    validHref(){
        return this.state.href.length > 0;
    }

    validCode(){
        return this.state.code.length >= 6;
    }

    validate(){
        let isValid = this.validHref();

        if(isValid && this.state.useCode){
            isValid = this.validCode()
        }

        return isValid;
    }

    getShortLinkExample($code){
        return !!$code && $code.length ? window.location.protocol + "//" + window.location.host+"/i/"+$code : "";
    }

    render() {
        const { errorList } = this.state;

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
                                   onChange={(e)=>this.handleHrefInput(e)}
                                   value={this.state.href}
                            />
                        </div>

                        <div className="form-group form-check">
                            <input type="checkbox"
                                   className="form-check-input"
                                   id="check1"
                                   onChange={(e)=>this.handleCheckbox(e)}
                            />
                            <label className="form-check-label"
                                   htmlFor="check1">Create your self code</label>
                        </div>

                        {
                            this.state.useCode ? (
                                <div className="form-group">
                                    <input type="text"
                                           className={"form-control"+(!this.validCode()?" is-invalid":"")}
                                           placeholder="Enter short-link code (min 6 symbols)"
                                           minLength="6"
                                           onChange={(e)=>this.handleCodeInput(e)}
                                           value={this.state.code}
                                    />
                                    <small className="text-muted">{ this.getShortLinkExample(this.state.code) }</small>
                                </div>
                            ) : null
                        }

                        <div className="form-group">
                            <button type="submit" className="btn btn-primary" disabled={!this.validate()}>{ !this.state.isLoading ? 'Shorten' : 'Loading...' } </button>
                        </div>

                    </form>

                    {
                        errorList.length ? <RenderErrors errors={errorList}/> : null
                    }

                </div>
            </div>

        )
    }
}