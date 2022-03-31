import React, { Component } from 'react';

import { Button }   from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import { UnitiesItem } from "@dashboardPages/components/Bill/Unity/UnitiesItem";

export class UnitiesList extends Component {
    render () {
        const { data, onChangeContext, taille, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une unité</Button>
                    </div>
                    <div className="item filter-search">
                        <Search onSearch={onSearch} placeholder="Recherche par unité"/>
                    </div>
                </div>

                <TopSorterPagination sorters={sorters} onSorter={onSorter}
                                     currentPage={currentPage} perPage={perPage} onPerPage={onPerPage} taille={taille} onClick={onPaginationClick}/>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-2">
                                        <div className="col-1">Unité</div>
                                        <div className="col-2 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <UnitiesItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}
