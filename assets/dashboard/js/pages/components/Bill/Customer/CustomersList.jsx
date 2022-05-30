import React, { Component } from 'react';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";
import { Filter, FilterSelected } from "@dashboardComponents/Layout/Filter";

import { CustomersContractItem, CustomersItem } from "@dashboardPages/components/Bill/Customer/CustomersItem";

export class CustomersList extends Component {
    constructor(props) {
        super(props);

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    render () {
        const { isPageContract=false, data, onChangeContext, taille, onGetFilters, filters, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter, onDeleteAll } = this.props;

        let filtersLabel = ["Afficher les factures"];
        let filtersId    = ["inv-display"];

        let itemsFilter = [
            { value: 0, id: filtersId[0], label: filtersLabel[0] },
        ];

        return <>
            <div>
                <div className={"toolbar" + (isPageContract ? " toolbar-customer" : "")}>
                    {!isPageContract && <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un client</Button>
                    </div>}
                    <div className="item filter-search">
                        {!isPageContract && <Filter ref={this.filter} items={itemsFilter} onGetFilters={onGetFilters} />}
                        <Search onSearch={onSearch} placeholder="Recherche par nom"/>
                        <FilterSelected filters={filters} itemsFiltersLabel={filtersLabel} itemsFiltersId={filtersId} onChange={this.handleFilter}/>
                    </div>
                </div>

                <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-header-selector" />
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-3">
                                        <div className="col-1">Client</div>
                                        <div className="col-2">Contact</div>
                                        <div className="col-3 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return !isPageContract ? <CustomersItem {...this.props} elem={elem} key={elem.id}/>
                                : <CustomersContractItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                {(!isPageContract && data && data.length !== 0) && <div className="page-actions">
                    <div className="selectors-actions">
                        <div className="item" onClick={onDeleteAll}>
                            <ButtonIcon icon="trash" text="Supprimer la sélection" />
                        </div>
                    </div>
                </div>}
            </div>
        </>
    }
}