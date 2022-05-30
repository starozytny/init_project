import React, { Component } from 'react';

import { Layout }        from "@dashboardComponents/Layout/Page";
import Sort              from "@commonComponents/functions/sort";

import { SocietiesList }       from "@dashboardPages/components/Bill/Society/SocietiesList";
import { SocietyFormulaire }   from "@dashboardPages/components/Bill/Society/SocietyForm";

const URL_DELETE_ELEMENT    = 'api_bill_societies_delete';
const MSG_DELETE_ELEMENT    = 'Supprimer cette société ?';
let SORTER = Sort.compareName;

export class Societies extends Component {
    constructor(props) {
        super(props);

        this.state = {
            perPage: 10,
            currentPage: 0,
            sorter: SORTER,
            pathDeleteElement: URL_DELETE_ELEMENT,
            msgDeleteElement: MSG_DELETE_ELEMENT,
            sessionName: "bill.societies.pagination",
            classes: props.classes ? props.classes : "main-content"
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

    handleSearch = (search) => { this.layout.current.handleSearch(search, "society"); }

    handleContentList = (currentData, changeContext) => {
        return <SocietiesList onChangeContext={changeContext}
                              onDelete={this.layout.current.handleDelete}
                              onSearch={this.handleSearch}
                              data={currentData} />
    }

    handleContentCreate = (changeContext) => {
        return <SocietyFormulaire type="create" onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    handleContentUpdate = (changeContext, element) => {
        return <SocietyFormulaire type="update" element={element} onChangeContext={changeContext} onUpdateList={this.handleUpdateList}/>
    }

    render () {
        return <>
            <Layout ref={this.layout} {...this.state} onGetData={this.handleGetData}
                    onContentList={this.handleContentList}
                    onContentCreate={this.handleContentCreate} onContentUpdate={this.handleContentUpdate}/>
        </>
    }
}