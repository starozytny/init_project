import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { SessionList }       from "./SessionList";

const URL_DELETE_ELEMENT    = 'api_sessions_delete';
const MSG_DELETE_ELEMENT    = 'Supprimer cette session de formation ?';
const URL_SWITCH_PUBLISHED  = 'api_registration_switch_attestation';
const MSG_SWITCH_PUBLISHED  = 'Attestation';
const SORTER = Sort.compareCreatedAt;

export class Session extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            sessionName: "sessions.participants.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSwitchAttestation = this.handleSwitchAttestation.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleSwitchAttestation = (element) => {
        this.layout.current.handleSwitchData(this, element.haveAttestation, Routing.generate(URL_SWITCH_PUBLISHED, {'id': element.id}),
            MSG_SWITCH_PUBLISHED, " désactivée", " autorisée");
    }

    handleContentList = (currentData, changeContext) => {
        return <SessionList onChangeContext={changeContext}
                            onDelete={this.layout.current.handleDelete}
                            onSwitchAttestation={this.handleSwitchAttestation}
                            sessionId={this.props.sessionId}
                            data={currentData} />
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList} />
        </>
    }
}