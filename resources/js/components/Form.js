import React, { Component } from 'react';
import axios from "axios";
import { RenderErrors } from "./Utils"

function getShortLinkExample(code){
    return !!code && code.length ? window.location.protocol + "//" + window.location.host+"/i/"+code : "";
}


export default class Form extends Component {

    constructor(props){
        super();
        this.state = {
            href: "",
            code: "",
            timeout: "",
            errorList: [],
            isLoading: false,
            useCode: false,
        };

        this.radios = [
            {title: 'infinite',value: ""},
            {title: '1 day',value: "day"},
            {title: '1 week',value: "week"},
            {title: '1 month',value: "month"}
        ];

        this.handleSubmit = this.handleSubmit.bind(this);
        this.validateForm = this.validateForm.bind(this);
        this.handleRadioChange = this.handleRadioChange.bind(this);
    }

    handleSubmit(e){
        e.preventDefault();

        this.setState({
            errorList: [],
            isLoading: true
        });

        let params = { href: this.state.href , timeout: this.state.timeout };

        if(this.state.useCode)
            params = { ...params , code: this.state.code };



        axios.post('/urls', params)
            .then(
                res => {
                    this.setState({
                        href: "",
                        code: "",
                        timeout: "",
                    });
                    if(this.props.onSuccesAdd){
                        this.props.onSuccesAdd(res.data);
                    }
                }
            )
            .catch(error => {
                let errorsList = [];
                if(!!error.response.data.message){
                    errorsList = [ ...errorsList, error.response.data.message ]
                }
                if(!!error.response.data.errors){
                    errorsList = [ ...errorsList, ...Object.values(error.response.data.errors) ];
                }
                this.setState({
                    errorList: errorsList
                });
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
            code: e.target.value.replace(/[^\w]/g, "")
        });
    }

    handleCheckbox(e) {
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

    validateForm(){
        let isValid = this.validHref();
        if(this.state.useCode){
            isValid = isValid && this.validCode()
        }
        return isValid;
    }

    handleRadioChange(e){
        this.setState({
            timeout: e.target.value
        });
    }

    render() {
        const { errorList , href , useCode , code, isLoading , timeout } = this.state;



        return (
            <div>
                <form onSubmit={this.handleSubmit}>
                    <div className="form-group">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Enter long URL-link here"
                            onChange={(e)=>this.handleHrefInput(e)}
                            value={href}
                        />
                    </div>

                    <div className="form-group ">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                className="form-check-input"
                                id="checkbox1"
                                onChange={(e)=>this.handleCheckbox(e)}
                            />
                            <label className="" htmlFor="checkbox1">Create self code</label>
                        </div>
                        <input
                            type="text"
                            className={"form-control"}
                            placeholder="Enter short code (min 6 symbols)"
                            minLength="6"
                            onChange={(e)=>this.handleCodeInput(e)}
                            value={code}
                            disabled={!useCode}
                        />
                        <small className="text-muted">{ getShortLinkExample(code) }</small>
                    </div>


                    <div className="form-group">
                        <label className="form-check-label">Lifetime:</label>
                        <div>
                            {
                                this.radios.map((radio,radioIndex) => {
                                    return (
                                        <div className="form-check form-check-inline" key={radioIndex}>
                                            <input
                                                className="form-check-input"
                                                type="radio"
                                                name="radio1"
                                                id={ "radio-"+radioIndex}
                                                value={ radio.value }
                                                onChange={this.handleRadioChange}
                                                checked={timeout === radio.value}
                                            />
                                            <label className="form-check-label" htmlFor={ "radio-"+radioIndex }>{ radio.title }</label>
                                        </div>
                                    )
                                })

                            }
                        </div>
                    </div>

                    <div className="">
                        <button
                            type="submit"
                            className="btn btn-success"
                            disabled={!this.validateForm()}
                        >{ !isLoading ? 'Create URL' : 'Loading...' } </button>
                    </div>

                </form>

                {
                    errorList.length ? <div className="mt-3"><RenderErrors className="" errors={errorList}/></div> : null
                }

            </div>

        )
    }
}