import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";
import Filter            from "@commonComponents/functions/filter";
import TopToolbar        from "@commonComponents/functions/topToolbar";

import { InvoicesList }      from "@dashboardPages/components/Bill/Invoice/InvoicesList";
import { InvoiceFormulaire } from "@dashboardPages/components/Bill/Invoice/InvoiceForm";

const URL_DELETE_ELEMENT    = 'api_bill_invoices_delete';
const MSG_DELETE_ELEMENT    = 'Supprimer ce brouillon ?';
let SORTER = Sort.compareNumeroInverse;

let sorters = [
    { value: 0, label: 'Numéro', identifiant: 'sorter-numero' },
]

let sortersFunction = [Sort.compareNumeroInverse];

export class Invoices extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            sessionName: "bill.invoices.pagination",
            society: props.society ? JSON.parse(props.society) : null,
            taxes: props.taxes ? JSON.parse(props.taxes) : [],
            unities: props.unities ? JSON.parse(props.unities) : [],
            items: props.items ? JSON.parse(props.items) : [],
            products: props.products ? JSON.parse(props.products) : [],
            customers: props.customers ? JSON.parse(props.customers) : [],
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handleGetFilters = this.handleGetFilters.bind(this);
        this.handlePerPage = this.handlePerPage.bind(this);
        this.handleChangeCurrentPage = this.handleChangeCurrentPage.bind(this);
        this.handleSorter = this.handleSorter.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleGetFilters = (filters) => { this.layout.current.handleGetFilters(filters, Filter.filterStatus); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "invoice", true, Filter.filterStatus); }

    handlePerPage = (perPage) => { TopToolbar.onPerPage(this, perPage, SORTER) }

    handleChangeCurrentPage = (currentPage) => { this.setState({ currentPage }); }

    handleSorter = (nb) => { SORTER = TopToolbar.onSorter(this, nb, sortersFunction, this.state.perPage) }

    handleContentList = (currentData, changeContext, getFilters, filters, data) => {
        const { perPage, currentPage, society } = this.state;

        return <InvoicesList onChangeContext={changeContext}
                             onDelete={this.layout.current.handleDelete}
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
                             society={society}
                             onUpdateList={this.handleUpdateList}
                             data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        const { society, items, taxes, unities, products, customers } = this.state;
        return <InvoiceFormulaire type="create" society={society} taxes={taxes} unities={unities} items={items} products={products} customers={customers}
                                  onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        const { society, items, taxes, unities, products, customers } = this.state;
        return <InvoiceFormulaire type="update" society={society} taxes={taxes} unities={unities} items={items} products={products} customers={customers}
                                  element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
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