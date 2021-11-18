import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { AlertsList }      from "./AlertsList";
import { AlertFormulaire } from "@dashboardFolder/js/pages/components/Alerts/AlertForm";

const URL_DELETE_ELEMENT = 'api_immo_alerts_delete';
const URL_DELETE_GROUP = 'api_immo_alerts_delete_group';
const MSG_DELETE_ELEMENT = 'Supprimer cette alerte ?';
const MSG_DELETE_GROUP = 'Aucune alerte sélectionnée.';
const SORTER = Sort.compareCreatedAtInverse;

export class Alerts extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sorter: SORTER,
            urlDeleteElement: Routing.generate(URL_DELETE_ELEMENT, {'token': element.token}),
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "alerts.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleContentList = (currentData, changeContext) => {
        return <AlertsList onChangeContext={changeContext}
                            onDelete={this.layout.current.handleDelete}
                            onDeleteAll={this.layout.current.handleDeleteGroup}
                            data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <AlertFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate}/>
        </>
    }
}