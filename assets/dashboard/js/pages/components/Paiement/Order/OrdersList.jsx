import React, { Component } from 'react';

import { Alert }              from "@dashboardComponents/Tools/Alert";
import { Search }             from "@dashboardComponents/Layout/Search";
import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Filter, FilterSelected } from "@dashboardComponents/Layout/Filter";

import { OrdersItem }   from "./OrdersItem";

export class OrdersList extends Component {
    constructor(props) {
        super(props);

        this.filter = React.createRef();

        this.handleFilter = this.handleFilter.bind(this);
    }

    handleFilter = (e) => {
        this.filter.current.handleChange(e, true);
    }

    render () {
        const { isDeveloper, data, onDeleteAll, onGetFilters, filters, onSearch, onProcess } = this.props;

        let filtersLabel = ["Attente", "Validé", "Traité", "Expiré", "Annulé"];
        let filtersId    = ["f-attente", "f-valider", "f-traiter", "f-expirer", "f-annuler"];

        let itemsFilter = [
            { value: 0, id: filtersId[0], label: filtersLabel[0] },
            { value: 1, id: filtersId[1], label: filtersLabel[1] },
            { value: 2, id: filtersId[2], label: filtersLabel[2] },
            { value: 3, id: filtersId[3], label: filtersLabel[3] },
            { value: 4, id: filtersId[4], label: filtersLabel[4] }
        ];

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onProcess("all")}>Traiter tous les ordres</Button>
                    </div>
                    <div className="item filter-search">
                        <Filter ref={this.filter} items={itemsFilter} onGetFilters={onGetFilters} />
                        <Search onSearch={onSearch} placeholder="Recherche par libelle, titulaire, montant ou email.."/>
                        <FilterSelected filters={filters} itemsFiltersLabel={filtersLabel} itemsFiltersId={filtersId} onChange={this.handleFilter}/>
                    </div>
                </div>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            {isDeveloper && <div className="item-header-selector" />}
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Ordre</div>
                                        <div className="col-2">Informations</div>
                                        <div className="col-3">Statut</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <OrdersItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                {(data && data.length !== 0 && isDeveloper) && <div className="page-actions">
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