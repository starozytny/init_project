import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@dashboardComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";

import { EstimationsList }      from "./EstimationsList";
import { EstimationFormulaire } from "@dashboardFolder/js/pages/components/Estimations/EstimationForm";

export class Estimations extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            sessionName: "estimations.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        this.handleDeleteGroup = this.handleDeleteGroup.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
    }

    handleGetData = (self) => { Formulaire.axiosGetDataPagination(self, Routing.generate('api_immo_estimations_index'), Sort.compareCreatedAtInverse, this.state.perPage) }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext, Sort.compareCreatedAtInverse); }

    handleDelete = (element) => {
        Formulaire.axiosDeleteElement(this, element, Routing.generate('api_immo_estimations_delete', {'token': element.token}),
            'Supprimer cette estimation ?', 'Cette action est irréversible.');
    }
    handleDeleteGroup = () => {
        let checked = document.querySelectorAll('.i-selector:checked');
        Formulaire.axiosDeleteGroupElement(this, checked, Routing.generate('api_immo_estimations_delete_group'), 'Aucune estimation sélectionné.')
    }

    handleContentList = (currentData, changeContext) => {
        return <EstimationsList onChangeContext={changeContext}
                                onDelete={this.handleDelete}
                                onDeleteAll={this.handleDeleteGroup}
                                data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <EstimationFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate}/>
        </>
    }
}