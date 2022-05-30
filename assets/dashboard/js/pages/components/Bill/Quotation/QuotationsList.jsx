import React, { Component } from 'react';

import Routing      from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { Filter, FilterSelected } from "@dashboardComponents/Layout/Filter";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import helper from "@dashboardPages/components/Bill/functions/helper";

import { QuotationsItem } from "@dashboardPages/components/Bill/Quotation/QuotationsItem";

const URL_INDEX_ELEMENTS = "admin_bill_quotations_index";

export class QuotationsList extends Component {
    constructor(props) {
        super(props);

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
        this.handleGenerate = this.handleGenerate.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    handleGenerate = (elem) => {
        helper.generateQuotation(this, elem)
    }

    render () {
        const { data, onChangeContext, taille, onGetFilters, filters, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter } = this.props;

        let filtersLabel = ["Brouillon", "En cours", "Accepté", "Refusé"];
        let filtersId    = ["f-br", "f-enc", "f-acc", 'f-ref'];

        let itemsFilter = [
            { value: 0, id: filtersId[0], label: filtersLabel[0] },
            { value: 1, id: filtersId[1], label: filtersLabel[1] },
            { value: 2, id: filtersId[2], label: filtersLabel[2] },
            { value: 3, id: filtersId[3], label: filtersLabel[3] },
        ];

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un devis</Button>
                    </div>
                    <div className="item filter-search">
                        <Filter ref={this.filter} items={itemsFilter} filters={filters} onGetFilters={onGetFilters} />
                        <Search onSearch={onSearch} placeholder="Recherche par numéro ou nom"/>
                        <FilterSelected filters={filters} itemsFiltersLabel={filtersLabel} itemsFiltersId={filtersId} onChange={this.handleFilter}/>
                    </div>
                </div>

                <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-7">
                                        <div className="col-1">Numéro</div>
                                        <div className="col-2">Client</div>
                                        <div className="col-3">Date</div>
                                        <div className="col-4">Date validité</div>
                                        <div className="col-5">Montant TTC</div>
                                        <div className="col-6">Statut</div>
                                        <div className="col-7 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <QuotationsItem {...this.props} elem={elem} key={elem.id}
                                                   onGenerate={this.handleGenerate}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>


                <div clasName="page-actions">
                    <div className="selectors-actions">
                        <div className="item">
                            <ButtonIcon element="a" icon="briefcase" text="Voir les archivés" onClick={Routing.generate(URL_INDEX_ELEMENTS, {'archive': 1})} />
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}
