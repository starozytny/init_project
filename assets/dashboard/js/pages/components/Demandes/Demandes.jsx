import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { DemandesList }      from "./DemandesList";
import { DemandeRead }       from "@dashboardFolder/js/pages/components/Demandes/DemandeRead";
import { DemandeFormulaire } from "@dashboardFolder/js/pages/components/Demandes/DemandeForm";

const URL_DELETE_ELEMENT = 'api_immo_demandes_delete';
const URL_DELETE_GROUP = 'api_immo_demandes_delete_group';
const MSG_DELETE_ELEMENT = 'Supprimer cette demande ?';
const MSG_DELETE_GROUP = 'Aucune demande sélectionnée.';
const URL_IS_SEEN = 'api_immo_demandes_isSeen';
const SORTER = Sort.compareCreatedAt;

export class Demandes extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "demandes.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
        this.handleChangeContextRead = this.handleChangeContextRead.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleContentList = (currentData, changeContext) => {
        return <DemandesList onChangeContext={changeContext}
                             bien={this.props.bien}
                             onDelete={this.layout.current.handleDelete}
                             onDeleteAll={this.layout.current.handleDeleteGroup}
                             data={currentData} />
    }

    handleContentRead = (changeContext, element) => {
        return <DemandeRead element={element} onChangeContext={changeContext}/>
    }

    handleChangeContextRead = (element) => {
        Formulaire.isSeen(this, element, Routing.generate(URL_IS_SEEN, {'id': element.id}))
    }

    handleContentCreate = (changeContext) => {
        return <DemandeFormulaire type="create" bien={this.props.bien} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate}
                    onContentRead={this.handleContentRead} onChangeContextRead={this.handleChangeContextRead}/>
        </>
    }
}