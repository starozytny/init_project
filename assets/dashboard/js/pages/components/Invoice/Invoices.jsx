import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { InvoicesList }      from "@dashboardPages/components/Invoice/InvoicesList";
import { InvoiceFormulaire } from "@dashboardPages/components/Invoice/InvoiceForm";

const URL_DELETE_ELEMENT    = 'api_bill_invoices_delete';
const MSG_DELETE_ELEMENT    = 'Supprimer cette facture ?';
let SORTER = Sort.compareCreatedAtInverse;

export class Invoices extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            sessionName: "invoices.pagination"
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
        this.handleContentCreate = this.handleContentCreate.bind(this);
        this.handleContentUpdate = this.handleContentUpdate.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "invoice"); }

    handleContentList = (currentData, changeContext) => {
        return <InvoicesList onChangeContext={changeContext}
                              onDelete={this.layout.current.handleDelete}
                              onSearch={this.handleSearch}
                              data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <InvoiceFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        return <InvoiceFormulaire type="update" element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}/>
        </>
    }
}
