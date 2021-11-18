import React, { Component } from 'react';

import { Page }          from "@dashboardComponents/Layout/Page";
import { LoaderElement } from "@dashboardComponents/Layout/Loader";
import Formulaire        from "@dashboardComponents/functions/Formulaire";
import Sort              from "@commonComponents/functions/sort";

import { AdsList } from "./AdsList";
import { AdItem } from "./AdItem";

function compareLabelThenZipcode(a, b) {
    if (a.label > b.label) {
        return 1;
    } else if (a.label < b.label) {
        return -1;
    }

    return Sort.comparison(a.address.zipcode, b.address.zipcode)
}

function filterByNature(data, nature)
{
    let currentData = [];
    if(data && data.length !== 0){
        data.forEach(elem => {
            if (elem.typeAd === nature) {
                currentData.push(elem);
            }else if(nature === "Vente" && (elem.typeAd === "Produit d'investissement" || elem.typeAd === "Vente de prestige")){
                currentData.push(elem);
            }
        })
    }

    return currentData;
}

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
            perPage: 20,
            nature: "Location",
            sessionName: "biens.pagination"
        }

        this.page = React.createRef();

        this.handleUpdateData = this.handleUpdateData.bind(this);
        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleChangeNature = this.handleChangeNature.bind(this);
    }

    componentDidMount() {
        const { donnees } = this.props;
        const { perPage, nature } = this.state;

        let data = JSON.parse(donnees);
        data.sort(Sort.compareAdPrice);

        let dataImmuable = data;
        data = filterByNature(data, nature)

        this.setState({ dataImmuable: dataImmuable, data: data, currentData: data.slice(0, perPage), loadPageError: false, loadData: false });
    }
    handleUpdateData = (data) => { this.setState({ currentData: data })  }
    handleUpdateList = (element, newContext=null) => {
        const { data, context, perPage, nature } = this.state

        Formulaire.updateDataPagination(this, Sort.compareAdPrice, newContext, context, filterByNature(data, nature), element, perPage);
    }
    handleChangeContext = (context, element=null) => {
        this.setState({ context, element });
        if(context === "list"){
            this.page.current.pagination.current.handleComeback()
        }
    }
    handleChangeNature = (label) => {
        const { dataImmuable, perPage } = this.state;

        let newData = filterByNature(dataImmuable, label)
        this.page.current.pagination.current.handlePageOne()
        this.setState({
            nature: label,
            data: newData,
            currentData: newData.slice(0,perPage)
        })
    }

    render () {
        const { loadPageError, context, loadData, data, currentData, element, perPage, nature, sessionName } = this.state;

        let content, havePagination = false;
        switch (context){
            case "show":
                content = loadData ? <LoaderElement /> : <AdItem onChangeContext={this.handleChangeContext}
                                                                 elem={element}/>
                break;
            default:
                havePagination = true;
                content = loadData ? <LoaderElement /> : <AdsList onChangeContext={this.handleChangeContext}
                                                                  onChangeNature={this.handleChangeNature}
                                                                  nature={nature}
                                                                  data={data}
                                                                  currentData={currentData} />
                break;
        }

        return <>
            <Page ref={this.page} haveLoadPageError={loadPageError} perPage={perPage} sessionName={sessionName}
                  havePagination={havePagination} taille={data && data.length} data={data} onUpdate={this.handleUpdateData}
            >
                {content}
            </Page>
        </>
    }
}