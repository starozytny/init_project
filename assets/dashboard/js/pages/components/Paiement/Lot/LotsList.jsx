import React, { Component } from 'react';

import { Alert }        from "@dashboardComponents/Tools/Alert";
import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

import { LotsItem }     from "@dashboardPages/components/Paiement/Lot/LotsItem";

export class LotsList extends Component {
    render () {
        const { isDeveloper, data, onDeleteAll } = this.props;

        return <>
            <div>
                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            {isDeveloper && <div className="item-header-selector" />}
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Daté le</div>
                                        <div className="col-2">Informations</div>
                                        <div className="col-3">Fichier</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <LotsItem {...this.props} elem={elem} key={elem.id}/>
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