import React, { Component } from 'react';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";
import { TopSorterPagination } from "@dashboardComponents/Layout/Pagination";

import { CustomersItem } from "@dashboardPages/components/Bill/Customer/CustomersItem";

export class CustomersList extends Component {
    render () {
        const { data, onChangeContext, taille, onSearch, perPage, onPerPage,
            onPaginationClick, currentPage, sorters, onSorter, onDeleteAll } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter un client</Button>
                    </div>
                    <div className="item filter-search">
                        <Search onSearch={onSearch} placeholder="Recherche par nom"/>
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
                            return <CustomersItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                {(data && data.length !== 0) && <div className="page-actions">
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
