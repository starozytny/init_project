import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import { DemandesItem }   from "./DemandesItem";

export class DemandesList extends Component {
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
                    <div className="items items-default items-contact">
                        {data && data.length !== 0 ? data.map(elem => {
                            return <DemandesItem {...this.props} elem={elem} key={elem.id}/>
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