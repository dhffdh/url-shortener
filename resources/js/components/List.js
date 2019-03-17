import React, { Component } from 'react';
import axios from "axios/index";


class Info extends Component {
    render() {
        const { stats } = this.props;
        return (
            <div className="fz-12">

                <div>Count of clicks: <b>{ stats.counter }</b></div>
                <div>Unique users for last 14 days: <b>{ stats.lastusers }</b></div>

                {
                    stats.clicks.length ? (
                        <div className="table-responsive  mt-2">
                            <table className="table table-sm">
                                <tbody>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">User IP</th>
                                </tr>
                                {
                                    stats.clicks.map((item,index) => {
                                        return (<tr key={index}>
                                            <td>{item.created_at}</td>
                                            <td>{item.user_ip}</td>
                                        </tr>)
                                    })
                                }
                                </tbody>
                            </table>
                        </div>
                    ) : null
                }

            </div>
        )
    }
}


class Item extends Component {


    constructor(props){
        super();
        this.state = {
            stats: false,
            showStats: false
        };
        this.onDeleteHandler = this.onDeleteHandler.bind(this);
        this.handleShowStatsClick = this.handleShowStatsClick.bind(this);
        this.closePopup = this.closePopup.bind(this);
    }

    onDeleteHandler(e,id){
        if(confirm('Are you sure you want to delete this item?')){
            axios.delete('/urls/'+id)
                .then(res => {
                    if(this.props.onDelete){
                        this.props.onDelete();
                    }
                })
                .catch(error => {
                    console.log('onDeleteHandler error', error)
                })
        }
    }

    handleShowStatsClick(e,id){
        e.preventDefault();
        if(!this.state.stats){
            axios.get('/urls/stat/'+id)
                .then(res => {
                    this.setState({
                        stats: res.data
                    }, () => {
                        this.togglePopup()
                    });
                })
                .catch(error => {
                    console.log('handleShowStatsClick error', error)
                })
        }else{
            this.togglePopup()
        }
    }

    togglePopup(){
        const newShowStats = !this.state.showStats;
        if(newShowStats){
            window.dispatchEvent( new Event('close-popups') );
        }
        this.setState({
            showStats: newShowStats
        });
    }

    closePopup(){
        this.setState({
            showStats: false
        });
    }

    componentDidMount() {
        window.addEventListener("close-popups", this.closePopup );
    }

    render() {
        const { url } = this.props;
        const { showStats , stats } = this.state;

        return (
            <div className="position-relative">
                <div className="d-flex justify-content-between position-relative">
                    <div className="pr-3 overflow-hidden">
                        <div className="font-weight-bolder"><a href={ url.short_href } target="_blank" >{ url.short_href }</a></div>
                        <div className="font-italic">{ url.href }</div>

                        <div className="text-muted small">
                            <span>id:{ url.id }</span>
                            {" | "}
                            <span>{ url.date }</span>
                            {" | "}
                            <a className="d-inline-block" href="#" onClick={(e) => this.handleShowStatsClick(e, url.id) } >{ !showStats ? 'Show stats' : 'Hide' }</a> </div>
                    </div>
                    <div className="d-flex flex-column">
                        <div className="clearfix">
                            <button type="button"
                                    className="close"
                                    onClick={ (e) => { this.onDeleteHandler(e,url.id) } }
                                    title="Delete"
                            ><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div className="mt-auto">

                        </div>
                    </div>
                </div>
                {
                    showStats ? <div className="b-stats-popup shadow bg-white p-2 border">
                        <button type="button"
                                className="close"
                                onClick={ this.closePopup }
                                title="Close"
                        ><span className="small">&times;</span></button>

                        <Info stats={stats} />

                    </div> : null
                }
            </div>
        )
    }
}

export default class List extends Component {

    constructor(props){
        super();
        this.onDeleteHandler = this.onDeleteHandler.bind(this);
    }

    onDeleteHandler(e){
        if(this.props.onDelete){
            this.props.onDelete();
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
                                <Item url={url} onDelete={this.onDeleteHandler} />
                            </li>
                        })
                    }
                </ul>
            </div>
        )
    }
}