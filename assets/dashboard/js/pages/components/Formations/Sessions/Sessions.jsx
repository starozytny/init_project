import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { SessionsList }       from "./SessionsList";
import { SessionsFormulaire } from "./SessionForm";

const URL_DELETE_ELEMENT    = 'api_sessions_delete';
const URL_DELETE_GROUP      = 'api_sessions_delete_group';
const MSG_DELETE_ELEMENT    = 'Supprimer cette session de formation ?';
const MSG_DELETE_GROUP      = 'Aucune session sélectionnée.';
const URL_SWITCH_PUBLISHED  = 'api_sessions_formation_published';
const MSG_SWITCH_PUBLISHED  = 'Session';
const SORTER = Sort.compareStart;

export class Sessions extends Component {
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
            sessionName: "sessions.formations.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSwitchPublished = this.handleSwitchPublished.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleSwitchPublished = (element) => {
        this.layout.current.handleSwitchPublished(this, element, Routing.generate(URL_SWITCH_PUBLISHED, {'id': element.id}), MSG_SWITCH_PUBLISHED);
    }

    handleContentList = (currentData, changeContext) => {
        return <SessionsList onChangeContext={changeContext}
                             onDelete={this.layout.current.handleDelete}
                             onDeleteAll={this.layout.current.handleDeleteGroup}
                             onSwitchPublished={this.handleSwitchPublished}
                             data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <SessionsFormulaire type="create" formationId={this.props.formation} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        return <SessionsFormulaire type="update" formationId={this.props.formation} element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate} />
        </>
    }
}