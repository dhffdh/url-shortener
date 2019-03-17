import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

import List from './List'
import Form from './Form'


export default class Main extends Component {

    constructor(props){
        super();
        this.state = {
            urls: [],
        };

        this.onSuccesAddHandler = this.onSuccesAddHandler.bind(this);
        this.onDeleteHandler = this.onDeleteHandler.bind(this);
    }

    componentDidMount() {
        this.getUrls();
    }

    getUrls() {

        axios.get('/urls')
            .then(res => {
                this.setState({
                    urls: res.data
                });
            })
            .catch(error => {
                console.log('getUrls error:', error)
            })
    }


    /**
     * Обработчик добавления ссылки
     */
    onSuccesAddHandler(){
        this.getUrls();
    }


    /**
     * Обработчик удаления ссылки
     *
     * @param id
     */
    onDeleteHandler(){
        this.getUrls();
    }

    render() {
        return (
            <div className="">
                <div>
                    <div className="card">
                        <div className="card-header">URL Shortener</div>
                        <div className="card-body">
                            <p>This tool will help you turn a long and complicated link into a short one.</p>
                            <Form onSuccesAdd={this.onSuccesAddHandler}/>
                        </div>
                    </div>
                </div>
                <div className="mt-4">
                    <List urls={this.state.urls} onDelete={this.onDeleteHandler}/>
                </div>
            </div>
        );
    }
}

if (document.getElementById('main')) {
    ReactDOM.render(<Main />, document.getElementById('main'));
}
