import React, { Component } from 'react';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }      from "@dashboardComponents/Tools/Alert";

import { AlertsItem }   from "./AlertsItem";

export class AlertsList extends Component {
    render () {
        const { data, onDeleteAll } = this.props;

        return <>
            <div>
                <div className="items-table">
                    <div className="items items-default items-contact">
                        {data && data.length !== 0 ? data.map(elem => {
                            return <AlertsItem {...this.props} elem={elem} key={elem.id}/>
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