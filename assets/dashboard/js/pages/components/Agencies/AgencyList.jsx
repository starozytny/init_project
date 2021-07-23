import React, { Component } from 'react';


import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import { AgencyItem }   from "./AgencyItem";

export class AgencyList extends Component {
    render () {
        const { data, onChangeContext, onDeleteAll, bien } = this.props;

        return <>
            <div>
                {bien && <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une demande</Button>
                    </div>
                </div>}
                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body item-body-image">
                                    <div className="infos infos-col-3">
                                        <div className="col-1">Agence</div>
                                        <div className="col-2">Email</div>
                                        <div className="col-3 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <AgencyItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>

                <div className="page-actions">
                    <div className="selectors-actions">
                        <div className="item" onClick={onDeleteAll}>
                            <ButtonIcon icon="trash" text="Supprimer la sélection" />
                        </div>
                    </div>
                </div>

            </div>
        </>
    }
}