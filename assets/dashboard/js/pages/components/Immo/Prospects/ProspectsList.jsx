import React, { Component } from 'react';

import { Alert }                  from "@dashboardComponents/Tools/Alert";
import { Button, ButtonIcon }     from "@dashboardComponents/Tools/Button";
import { Search }                 from "@dashboardComponents/Layout/Search";

import { ProspectsItem }   from "./ProspectsItem";
import {Filter, FilterSelected} from "@dashboardComponents/Layout/Filter";
import {TopSorterPagination} from "@dashboardComponents/Layout/Pagination";

export class ProspectsList extends Component {
    constructor(props) {
        super(props);

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    render () {
        const { isSelect=false, isFromRead=false, isClient, data, onSearch, onChangeContext, onDeleteAll, onGetFilters, filters,
            sorters, onSorter, currentPage, perPage, onPerPage, taille, onPaginationClick } = this.props;

        let filtersLabel = ["Aucun", "En recherche", "A compléter", "A contacter", "En offre"];
        let filtersId    = ["f-aucun", "f-search", "f-complete", 'f-contact', 'f-offer'];

        let itemsFilter = [
            { value: 0, id: filtersId[0], label: filtersLabel[0] },
            { value: 1, id: filtersId[1], label: filtersLabel[1] },
            { value: 2, id: filtersId[2], label: filtersLabel[2] },
            { value: 3, id: filtersId[3], label: filtersLabel[3] },
            { value: 4, id: filtersId[4], label: filtersLabel[4] }
        ];

        return <>
            <div>
                {!isSelect && <div className="toolbar toolbar-prospect">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un prospect</Button>
                    </div>
                    {isFromRead && <div className="item">
                        <Button onClick={() => onChangeContext("select")}>Sélectionner un existant</Button>
                    </div>}
                    {!isFromRead && <div className="item filter-search">
                        <Filter ref={this.filter} items={itemsFilter} onGetFilters={onGetFilters} />
                        <Search onSearch={onSearch} placeholder="Recherche par nom, prénom.."/>
                        <FilterSelected filters={filters} itemsFiltersLabel={filtersLabel} itemsFiltersId={filtersId} onChange={this.handleFilter}/>
                    </div>}
                </div>}

                {!isFromRead && <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>}

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            {(!isClient || isSelect) && <div className="item-header-selector" />}
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Prospect</div>
                                        <div className="col-3">Status</div>
                                        <div className="col-3">Négociateur</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <ProspectsItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                {(!isFromRead && data && data.length !== 0 && !isClient) && <div className="page-actions">
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