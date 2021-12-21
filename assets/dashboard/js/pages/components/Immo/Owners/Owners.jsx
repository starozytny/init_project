import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { OwnersList }       from "./OwnersList";
import { OwnerFormulaire }  from "./OwnerForm";

const URL_DELETE_ELEMENT = 'api_owners_delete';
const URL_DELETE_GROUP   = 'api_owners_delete_group';
const MSG_DELETE_ELEMENT = 'Supprimer ce négociateur ?';
const MSG_DELETE_GROUP   = 'Aucun négociateur sélectionnés.';
const SORTER = Sort.compareLastname;

export class Owners extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "societies.pagination",
            societies: props.societies ? JSON.parse(props.societies) : [],
            isClient: props.isClient ? props.isClient : false
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "owner"); }

    handleContentList = (currentData, changeContext) => {
        return <OwnersList onChangeContext={changeContext}
                           onDelete={this.layout.current.handleDelete}
                           onDeleteAll={this.layout.current.handleDeleteGroup}
                           onSearch={this.handleSearch}
                           isClient={this.state.isClient}
                           data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        const { societies, isClient } = this.state;
        return <OwnerFormulaire type="create" societies={societies} isClient={isClient}
                                onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        const { societies, isClient } = this.state;
        return <OwnerFormulaire type="update" societies={societies} isClient={isClient} element={element}
                                onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}/>
        </>
    }
}