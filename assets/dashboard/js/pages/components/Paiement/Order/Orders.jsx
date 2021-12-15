import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { OrdersList }      from "./OrdersList";

const URL_DELETE_ELEMENT    = 'api_contact_delete';
const URL_DELETE_GROUP      = 'api_contact_delete_group';
const MSG_DELETE_ELEMENT    = 'Supprimer ce message ?';
const MSG_DELETE_GROUP      = 'Aucun message sélectionné.';
const SORTER = Sort.compareCreatedAt;

function filterFunction(dataImmuable, filters){
    let newData = [];
    if(filters.length === 0) {
        newData = dataImmuable
    }else{
        dataImmuable.forEach(el => {
            filters.forEach(filter => {
                if(filter === el.status){
                    newData.filter(elem => elem.id !== el.id)
                    newData.push(el);
                }
            })
        })
    }

    return newData;
}

export class Orders extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "orders.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleGetFilters = this.handleGetFilters.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleGetFilters = (filters) => { this.layout.current.handleGetFilters(filters, filterFunction); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "orders", true, filterFunction); }

    handleContentList = (currentData, changeContext, getFilters, filters) => {
        return <OrdersList onChangeContext={changeContext}
                           onDelete={this.layout.current.handleDelete}
                           onDeleteAll={this.layout.current.handleDeleteGroup}
                           onSearch={this.handleSearch}
                           filters={filters}
                           onGetFilters={this.handleGetFilters}
                           data={currentData} />
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}/>
        </>
    }
}