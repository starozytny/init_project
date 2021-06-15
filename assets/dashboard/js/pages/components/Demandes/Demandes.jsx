import React, { Component } from 'react';

import axios             from "axios";
import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@dashboardComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { DemandesList }      from "./DemandesList";
import { DemandeRead }       from "@dashboardFolder/js/pages/components/Demandes/DemandeRead";
import { DemandeFormulaire } from "@dashboardFolder/js/pages/components/Demandes/DemandeForm";

export class Demandes extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sessionName: "demandes.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteGroup = this.handleDeleteGroup.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
        this.handleChangeContextRead = this.handleChangeContextRead.bind(this);
    }

    handleGetData = (self) => { Formulaire.axiosGetDataPagination(self, Routing.generate('api_immo_demandes_index'), Sort.compareCreatedAt, this.state.perPage) }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, Sort.compareCreatedAt); }

    handleDelete = (element) => {
        Formulaire.axiosDeleteElement(this, element, Routing.generate('api_immo_demandes_delete', {'token': element.token}),
            'Supprimer cette demande ?', 'Cette action est irréversible.');
    }
    handleDeleteGroup = () => {
        let checked = document.querySelectorAll('.i-selector:checked');
        Formulaire.axiosDeleteGroupElement(this, checked, Routing.generate('api_immo_demandes_delete_group'), 'Aucune demande sélectionnée.')
    }

    handleContentList = (currentData, changeContext) => {
        return <DemandesList onChangeContext={changeContext}
                             bien={this.props.bien}
                             onDelete={this.handleDelete}
                             onDeleteAll={this.handleDeleteGroup}
                             data={currentData} />
    }

    handleContentRead = (changeContext, element) => {
        return <DemandeRead element={element} onChangeContext={changeContext}/>
    }

    handleChangeContextRead = (element) => {
        if(!element.isSeen){
            const self = this;
            axios.post(Routing.generate('api_immo_demandes_isSeen', {'id': element.id}), {})
                .then(function (response) {
                    let data = response.data;
                    self.handleUpdateList(data, 'update');
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error)
                })
            ;
        }
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