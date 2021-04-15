import React, { Component } from 'react';

import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Page }          from "@dashboardComponents/Layout/Page";
import { LoaderElement } from "@dashboardComponents/Layout/Loader";
import Formulaire        from "@dashboardComponents/functions/Formulaire";
import Sort              from "@dashboardComponents/functions/sort";

import { AdsList } from "./AdsList";

export class Ads extends Component {
    constructor(props) {
        super();

        this.state = {
            context: "list",
            loadPageError: false,
            loadData: true,
            data: null,
            currentData: null,
            element: null,
            filters: [],
            perPage: 20
        }

        this.page = React.createRef();

        this.handleUpdateData = this.handleUpdateData.bind(this);
        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
    }

    componentDidMount() { Formulaire.axiosGetDataPagination(this, Routing.generate('api_ads_index'), this.state.perPage) }
    handleUpdateData = (data) => { this.setState({ currentData: data })  }
    handleUpdateList = (element, newContext=null) => {
        const { data, context, perPage } = this.state
        Formulaire.updateDataPagination(this, Sort.compareName, newContext, context, data, element, perPage);
    }
    handleChangeContext = (context, element=null) => {
        this.setState({ context, element });
        if(context === "list"){
            this.page.current.pagination.current.handleComeback()
        }
    }

    render () {
        const { loadPageError, context, loadData, data, currentData, element, perPage, filters } = this.state;

        let content, havePagination = false;
        switch (context){
            default:
                havePagination = true;
                content = loadData ? <LoaderElement /> : <AdsList onChangeContext={this.handleChangeContext}
                                                                   data={currentData} />
                break;
        }

        return <>
            <Page ref={this.page} haveLoadPageError={loadPageError} perPage={perPage}
                  havePagination={havePagination} taille={data && data.length} data={data} onUpdate={this.handleUpdateData}
            >
                {content}
            </Page>
        </>
    }
}