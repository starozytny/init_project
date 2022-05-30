import React, { Component } from 'react';

import axios             from "axios";
import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Layout }        from "@dashboardComponents/Layout/Page";

import Sort              from "@commonComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";
import TopToolbar        from "@commonComponents/functions/topToolbar";

import { ContractsList } from "@dashboardPages/components/Bill/Contract/ContractsList";
import { ContractFormulaire } from "@dashboardPages/components/Bill/Contract/ContractForm";

const URL_RELATION_ELEMENT  = 'api_bill_contracts_relation_link';
const URL_DELETE_ELEMENT    = 'api_bill_contracts_delete';
const MSG_DELETE_ELEMENT    = 'Supprimer ce contrat ?';
let SORTER = Sort.compareNumeroInverse;

let sorters = [
    { value: 0, label: 'Nom', identifiant: 'sorter-name' },
]

let sortersFunction = [Sort.compareName];

export class Contracts extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            sessionName: "bill.contracts.pagination",
            relations: props.relations ? JSON.parse(props.relations) : [],
            taxes: props.taxes ? JSON.parse(props.taxes) : [],
            unities: props.unities ? JSON.parse(props.unities) : [],
            items: props.items ? JSON.parse(props.items) : [],
            products: props.products ? JSON.parse(props.products) : [],
            customers: props.customers ? JSON.parse(props.customers) : [],
            classes: props.classes ? props.classes : "main-content",
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handlePerPage = this.handlePerPage.bind(this);
        this.handleChangeCurrentPage = this.handleChangeCurrentPage.bind(this);
        this.handleSorter = this.handleSorter.bind(this);
        this.handleUpdateRelation = this.handleUpdateRelation.bind(this);
        this.handleChangeRelation = this.handleChangeRelation.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "contract"); }

    handlePerPage = (perPage) => { TopToolbar.onPerPage(this, perPage, SORTER) }

    handleChangeCurrentPage = (currentPage) => { this.setState({ currentPage }); }

    handleSorter = (nb) => { SORTER = TopToolbar.onSorter(this, nb, sortersFunction, this.state.perPage) }

    handleUpdateRelation = (data, context) => {
        const { relations } = this.state;


        if(context === "create"){
            this.setState({ relations: [...relations, ...[data]] })
        }else if(context === "update"){
            let nRelations = [];

            relations.forEach(el => {
                if(el.id === data.id){
                    el = data;
                }
                nRelations.push(el);
            })
            this.setState({ relations: nRelations })
        }else{
            let nRelations = relations.filter(el => { return el.id !== data }) //only for change relation delete
            this.setState({ relations: nRelations })
        }
    }

    handleChangeRelation = (type, element, obj, context) => {
        const { societyId } = this.props;

        Formulaire.loader(true);
        let self = this;
        axios({ method: "POST", url: Routing.generate(URL_RELATION_ELEMENT, {'type': type, 'id': element.id, 'customer': obj.id}), data: {societyId: societyId} })
            .then(function (response) {
                let data = response.data;

                self.handleUpdateRelation(data, context);
            })
            .catch(function (error) {
                Formulaire.displayErrors(self, error);
            })
            .then(() => {
                Formulaire.loader(false);
            })
        ;
    }

    handleContentList = (currentData, changeContext, getFilters, filters, data) => {
        const { customers, sites } = this.props;
        const { perPage, currentPage, relations } = this.state;

        return <ContractsList onChangeContext={changeContext}
                             //filter-search
                             onSearch={this.handleSearch}
                             onDelete={this.layout.current.handleDelete}
                             //changeNumberPerPage
                             perPage={perPage}
                             onPerPage={this.handlePerPage}
                             //twice pagination
                             currentPage={currentPage}
                             onPaginationClick={this.layout.current.handleGetPaginationClick(this)}
                             taille={data.length}
                             //sorter
                             sorters={sorters}
                             onSorter={this.handleSorter}
                             //data
                             relations={relations}
                             customers={customers}
                              sites={sites}
                             onChangeRelation={this.handleChangeRelation}
                             onUpdateRelation={this.handleUpdateRelation}
                             data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        const { societyId, counterContract, yearContract } = this.props;
        const { taxes, unities, items, products } = this.state;
        return <ContractFormulaire type="create" societyId={societyId} counterContract={counterContract} yearContract={yearContract}
                                   taxes={taxes} unities={unities} items={items} products={products}
                                   onChangeContext={changeContext} onUpdateList={this.handleUpdateList} />
    }

    handleContentUpdate = (changeContext, element) => {
        const { societyId, counterContract, yearContract } = this.props;
        const { taxes, unities, items, products } = this.state;
        return <ContractFormulaire type="update" societyId={societyId} counterContract={counterContract} yearContract={yearContract}
                                   taxes={taxes} unities={unities} items={items} products={products}
                                   element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList} />
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}
                    onChangeCurrentPage={this.handleChangeCurrentPage} />
        </>
    }
}
