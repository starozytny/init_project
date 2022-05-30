import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";

import Sort              from "@commonComponents/functions/sort";
import TopToolbar        from "@commonComponents/functions/topToolbar";

import { ContractsListProcess } from "@dashboardPages/components/Bill/Contract/ContractsList";

let SORTER = Sort.compareNumeroInverse;

let sorters = [
    { value: 0, label: 'Nom', identifiant: 'sorter-name' },
]

let sortersFunction = [Sort.compareName];

export class ContractsProcess extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            sessionName: "bill.contracts.process.pagination",
            year: parseInt(props.year),
            month: parseInt(props.month),
            society: props.society ? JSON.parse(props.society) : null,
            relations: props.relations ? JSON.parse(props.relations) : [],
            invoices: props.invoices ? JSON.parse(props.invoices) : [],
            classes: props.classes ? props.classes : "main-content",
        }

        this.layout = React.createRef();

        this.handleGetData = this.handleGetData.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.handlePerPage = this.handlePerPage.bind(this);
        this.handleChangeCurrentPage = this.handleChangeCurrentPage.bind(this);
        this.handleSorter = this.handleSorter.bind(this);

        this.handleContentList = this.handleContentList.bind(this);
    }

    handleGetData = (self) => { self.handleSetDataPagination(this.props.donnees); }

    handleUpdateList = (element, newContext=null) => { this.layout.current.handleUpdateList(element, newContext); }

    handleUpdateRelations = (element, context) => {
        const { relations } = this.state;

        if(context === "create"){
            this.setState({ relations: [...relations, ...[element]] })
        }else{
            let nRelations = [];
            relations.forEach(rel => {
                if(rel.id === element.id){
                    rel = element;
                }

                nRelations.push(rel);
            })
            this.setState({ relations: nRelations })
        }
    }

    handleUpdateInvoices = (element, context) => {
        const { invoices } = this.state;

        if(context === "create"){
            this.setState({ invoices: [...invoices, ...[element]] })
        }else{
            let nInvoices = [];
            invoices.forEach(rel => {
                if(rel.id === element.id){
                    rel = element;
                }

                nInvoices.push(rel);
            })
            this.setState({ invoices: nInvoices })
        }
    }

    handleSearch = (search) => { this.layout.current.handleSearch(search, "contract"); }

    handlePerPage = (perPage) => { TopToolbar.onPerPage(this, perPage, SORTER) }

    handleChangeCurrentPage = (currentPage) => { this.setState({ currentPage }); }

    handleSorter = (nb) => { SORTER = TopToolbar.onSorter(this, nb, sortersFunction, this.state.perPage) }

    handleContentList = (currentData, changeContext, getFilters, filters, data) => {
        const { perPage, currentPage, relations, year, month, society, invoices } = this.state;

        return <ContractsListProcess onChangeContext={changeContext}
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
                             onUpdateList={this.handleUpdateList}
                             onUpdateListCustom={this.handleUpdateRelations}
                             onUpdateInvoices={this.handleUpdateInvoices}
                             society={society}
                             relations={relations}
                             invoices={invoices}
                             year={year}
                             month={month}
                             data={currentData} />
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onChangeCurrentPage={this.handleChangeCurrentPage} />
        </>
    }
}
