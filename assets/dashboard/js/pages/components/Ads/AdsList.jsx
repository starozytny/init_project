import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";

import { Filter, FilterSelected } from "@dashboardComponents/Layout/Filter";
import { Search }     from "@dashboardComponents/Layout/Search";
import { Alert }      from "@dashboardComponents/Tools/Alert";

import { AdsItem }   from "./AdsItem";

export class AdsList extends Component {
    constructor(props) {
        super(props);

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    render () {
        const { data, onChangeContext, onGetFilters, filters, onSearch, onDeleteAll } = this.props;

        // let itemsFiltersLabelArray = ["Utilisateur", "Développeur", "Administrateur"];
        // let itemsFiltersIdArray = ["f-user", "f-dev", "f-admin"];
        //
        // let itemsFilter = [
        //     { value: 0, id: itemsFiltersIdArray[0], label: itemsFiltersLabelArray[0]},
        //     { value: 1, id: itemsFiltersIdArray[1], label: itemsFiltersLabelArray[1] },
        //     { value: 2, id: itemsFiltersIdArray[2], label: itemsFiltersLabelArray[2]}
        // ]

        return <>
            <div>
                {/*<div className="toolbar">*/}
                {/*    <div className="item filter-search">*/}
                {/*        <Filter ref={this.filter} items={itemsFilter} onGetFilters={onGetFilters} />*/}
                {/*        <Search onSearch={onSearch} />*/}
                {/*        <FilterSelected filters={filters} itemsFiltersLabel={itemsFiltersLabelArray} itemsFiltersId={itemsFiltersIdArray} onChange={this.handleFilter}/>*/}
                {/*    </div>*/}
                {/*</div>*/}

                <div className="items-table">
                    <div className="items items-default items-user items-ad">
                        <div className="item item-header">
                            <div className="selector">#</div>
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="avatar" />
                                    <div className="infos">
                                        <div className="ad-info">Bien</div>
                                        <div className="ad-price">Prix</div>
                                        <div className="ad-ad">Type annonce</div>
                                        <div className="ad-bien">Type bien</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <AdsItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                <div className="page-actions">
                    <div className="common-actions">
                        <div className="item">
                            <div className="dropdown">
                                <div className="dropdown-btn">
                                    <span className="icon-download" />
                                    <span>Exporter</span>
                                </div>
                                <div className="dropdown-items">
                                    <a className="item" download="utilisateurs.csv" href={Routing.generate('api_users_export', {'format': 'csv'})}>
                                        <ButtonIcon icon="file" text="Exporter en CSV" />
                                    </a>
                                    <a className="item" download="utilisateurs.xlsx" href={Routing.generate('api_users_export', {'format': 'excel'})}>
                                        <ButtonIcon icon="file" text="Exporter en Excel" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </>
    }
}