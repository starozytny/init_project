import React, { Component } from 'react';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Alert }      from "@dashboardComponents/Tools/Alert";

import {HeaderSession, SessionsItem} from "./SessionsItem";

export class SessionsList extends Component {
    render () {
        const { data, onChangeContext, onDeleteAll } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une session</Button>
                    </div>
                </div>

                <div className="items-table">
                    <div className="items items-default">
                        <HeaderSession haveSelector={true}/>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <SessionsItem {...this.props} elem={elem} key={elem.id}/>
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