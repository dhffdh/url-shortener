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
                //console.log('/urls',res.data);
                this.setState({
                    urls: res.data
                });
            })
            .catch(error => {
                //console.log('ERROR', error)
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
    onDeleteHandler(id){

        if(confirm('Are you sure you want to delete this item?')){
            axios.delete('/urls/'+id+"/")
                .then(res => {
                    this.getUrls();
                })
                .catch(error => {
                    console.log('delete error', error)
                })
        }

    }

    render() {
        const {urls} = this.state;

        return (
            <div className="">
                <div>
                    <Form onSuccesAdd={this.onSuccesAddHandler}/>
                </div>
                <div className="mt-4">
                    <List urls={urls} onDelete={this.onDeleteHandler}/>
                </div>
            </div>
        );
    }
}

if (document.getElementById('main')) {
    ReactDOM.render(<Main />, document.getElementById('main'));
}
