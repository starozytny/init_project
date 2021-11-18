import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { DevisList }        from "./DevisList";
import { DevisRead }        from "@dashboardFolder/js/pages/components/Devis/DevisRead";
import { DevisFormulaire }  from "@dashboardFolder/js/pages/components/Devis/DevisForm";

const URL_DELETE_ELEMENT = 'api_immo_devis_delete';
const URL_DELETE_GROUP = 'api_immo_devis_delete_group';
const MSG_DELETE_ELEMENT = 'Supprimer ce devis ?';
const MSG_DELETE_GROUP = 'Aucun devis sélectionné.';
const SORTER = Sort.compareCreatedAtInverse;

export class Devis extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "devis.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleContentList = (currentData, changeContext) => {
        return <DevisList onChangeContext={changeContext}
                          onDelete={this.handleDelete}
                          onDeleteAll={this.handleDeleteGroup}
                          data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <DevisFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentRead = (changeContext, element) => {
        return <DevisRead elem={element} onChangeContext={changeContext} />
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentRead={this.handleContentRead}
                    onContentCreate={this.handleContentCreate}/>
        </>
    }
}