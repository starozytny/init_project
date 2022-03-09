import React, { Component } from 'react';

import { Button }   from "@dashboardComponents/Tools/Button";
import { Alert }    from "@dashboardComponents/Tools/Alert";
import { Search }   from "@dashboardComponents/Layout/Search";

import { InvoicesItem }   from "@dashboardPages/components/Invoice/InvoicesItem";

export class InvoicesList extends Component {
    render () {
        const { data, onChangeContext, onSearch } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une facture</Button>
                    </div>
                    <div className="item filter-search">
                        <Search onSearch={onSearch} placeholder="Recherche par numéro ou nom.."/>
                    </div>
                </div>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Client</div>
                                        <div className="col-2">Informations</div>
                                        <div className="col-3">Statut</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <InvoicesItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}
