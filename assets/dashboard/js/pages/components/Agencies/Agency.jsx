import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@dashboardComponents/functions/sort";

import { AgencyList }       from "./AgencyList";
import { AgencyRead }       from "@dashboardFolder/js/pages/components/Agencies/AgencyRead";
import { AgencyFormulaire } from "@dashboardFolder/js/pages/components/Agencies/AgencyForm";

const URL_DELETE_ELEMENT = 'api_immo_agency_delete';
const MSG_DELETE_ELEMENT = 'Supprimer cette agence ?';
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
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees, SORTER); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, SORTER); }

    handleDelete = (element) => {
        this.layout.current.handleDelete(this, element, Routing.generate(URL_DELETE_ELEMENT, {'id': element.id}), MSG_DELETE_ELEMENT);
    }

    handleContentList = (currentData, changeContext) => {
        return <AgencyList onChangeContext={changeContext}
                           total={this.props.total}
                           onDelete={this.handleDelete}
                           data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <AgencyFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        return <AgencyFormulaire type="update" element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentRead = (changeContext, element) => {
        return <AgencyRead element={element} onChangeContext={changeContext}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}
                    onContentRead={this.handleContentRead} />
        </>
    }
}