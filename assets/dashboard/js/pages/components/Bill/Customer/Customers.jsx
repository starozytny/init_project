import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";

import Sort              from "@commonComponents/functions/sort";
import Formulaire        from "@dashboardComponents/functions/Formulaire";
import TopToolbar        from "@commonComponents/functions/topToolbar";
import Filter            from "@commonComponents/functions/filter";

import { CustomersList } from "@dashboardPages/components/Bill/Customer/CustomersList";
import { CustomerFormulaire } from "@dashboardPages/components/Bill/Customer/CustomerForm";

const URL_DELETE_ELEMENT    = 'api_bill_customers_delete';
const URL_DELETE_GROUP      = 'api_bill_customers_delete_group';
const MSG_DELETE_ELEMENT    = 'Supprimer ce client ?';
const MSG_DELETE_GROUP      = 'Aucun client sélectionné.';
let SORTER = Sort.compareName;
let SORTER_SITE = Sort.compareName;

let sorters = [
    { value: 0, label: 'Nom', identifiant: 'sorter-name' },
]

let sortersFunction = [Sort.compareName];

export class Customers extends Component {
    constructor(props) {
        super(props);

        let sites = [];
        if(props.sites){
            sites = JSON.parse(props.sites);
            sites.sort(SORTER_SITE);
        }

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            pathDeleteGroup: URL_DELETE_GROUP,
            msgDeleteGroup: MSG_DELETE_GROUP,
            sessionName: "bill.customer.pagination",
            invoices: props.invoices ? JSON.parse(props.invoices) : [],
            sites: sites,
            classes: props.classes ? props.classes : "main-content",
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handlePerPage = this.handlePerPage.bind(this);
        this.handleChangeCurrentPage = this.handleChangeCurrentPage.bind(this);
        this.handleSorter = this.handleSorter.bind(this);
        this.handleUpdateListSites = this.handleUpdateListSites.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleGetFilters = (filters) => { this.layout.current.handleGetFilters(filters, Filter.filterDisplayInvoice); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "customer", true, Filter.filterDisplayInvoice); }

    handlePerPage = (perPage) => { TopToolbar.onPerPage(this, perPage, SORTER) }

    handleChangeCurrentPage = (currentPage) => { this.setState({ currentPage }); }

    handleSorter = (nb) => { SORTER = TopToolbar.onSorter(this, nb, sortersFunction, this.state.perPage) }

    handleUpdateListSites = (element, context) => {
        let newSites = Formulaire.updateDataPagination(SORTER_SITE, context, context, this.state.sites, element);
        this.setState({ sites: newSites })
    }

    handleContentList = (currentData, changeContext, getFilters, filters, data) => {
        const { isPageContract, onChangeRelation, contract, relations } = this.props;
        const { perPage, currentPage, invoices, sites } = this.state;

        return <CustomersList onChangeContext={changeContext}
                              onDelete={this.layout.current.handleDelete}
                              onDeleteAll={this.layout.current.handleDeleteGroup}
                              //filter-search
                              onSearch={this.handleSearch}
                              filters={filters}
                              onGetFilters={this.handleGetFilters}
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
                              isPageContract={isPageContract}
                              onChangeRelation={onChangeRelation}
                              contract={contract}
                              relations={relations}
                              invoices={invoices}
                              sites={sites}
                              data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        const { societyId, counterCustomer, yearCustomer } = this.props;
        const { sites } = this.state;
        return <CustomerFormulaire type="create" societyId={societyId} sites={sites} counterCustomer={counterCustomer} yearCustomer={yearCustomer}
                                   onChangeContext={changeContext} onUpdateList={this.handleUpdateList}
                                   onUpdateListSites={this.handleUpdateListSites}/>
    }

    handleContentUpdate = (changeContext, element) => {
        const { societyId, counterCustomer, yearCustomer } = this.props;
        const { sites } = this.state;
        return <CustomerFormulaire type="update" societyId={societyId} sites={sites} counterCustomer={counterCustomer} yearCustomer={yearCustomer}
                                   element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}
                                   onUpdateListSites={this.handleUpdateListSites}/>
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
