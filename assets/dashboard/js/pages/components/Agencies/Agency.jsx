import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@dashboardComponents/functions/sort";

import { AgencyList }      from "./AgencyList";
import { AgencyRead }       from "@dashboardFolder/js/pages/components/Agencies/AgencyRead";

const URL_DELETE_ELEMENT = 'api_immo_demandes_delete';
const URL_DELETE_GROUP = 'api_immo_demandes_delete_group';
const MSG_DELETE_ELEMENT = 'Supprimer cette demande ?';
const MSG_DELETE_GROUP = 'Aucune demande sélectionnée.';
const SORTER = Sort.compareName;

export class Agency extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sessionName: "agencies.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteGroup = this.handleDeleteGroup.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees, SORTER); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, SORTER); }

    handleDelete = (element) => {
        this.layout.current.handleDelete(this, element, Routing.generate(URL_DELETE_ELEMENT, {'id': element.id}), MSG_DELETE_ELEMENT);
    }

    handleDeleteGroup = () => {
        this.layout.current.handleDeleteGroup(this, Routing.generate(URL_DELETE_GROUP), MSG_DELETE_GROUP);
    }

    handleContentList = (currentData, changeContext) => {
        return <AgencyList onChangeContext={changeContext}
                           bien={this.props.bien}
                           onDelete={this.handleDelete}
                           onDeleteAll={this.handleDeleteGroup}
                           data={currentData} />
    }

    handleContentRead = (changeContext, element) => {
        return <AgencyRead element={element} onChangeContext={changeContext}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentRead={this.handleContentRead} />
        </>
    }
}