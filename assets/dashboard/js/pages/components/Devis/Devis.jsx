import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@dashboardComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { DevisList }        from "./DevisList";
import { DevisRead }        from "@dashboardFolder/js/pages/components/Devis/DevisRead";
import { DevisFormulaire }  from "@dashboardFolder/js/pages/components/Devis/DevisForm";

export class Devis extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sessionName: "devis.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteGroup = this.handleDeleteGroup.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentRead = this.handleContentRead.bind(this);
    }

    handleGetData = (self) => { Formulaire.axiosGetDataPagination(self, Routing.generate('api_immo_devis_index'), Sort.compareCreatedAtInverse, this.state.perPage) }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, Sort.compareCreatedAtInverse); }

    handleDelete = (element) => {
        Formulaire.axiosDeleteElement(this, element, Routing.generate('api_immo_devis_delete', {'id': element.id}),
            'Supprimer ce devis ?', 'Cette action est irréversible.');
    }
    handleDeleteGroup = () => {
        let checked = document.querySelectorAll('.i-selector:checked');
        Formulaire.axiosDeleteGroupElement(this, checked, Routing.generate('api_immo_devis_delete_group'), 'Aucun devis sélectionné.')
    }

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