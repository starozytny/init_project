import React, { Component } from 'react';

import { Button }       from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import { AgencyItem }   from "./AgencyItem";

export class AgencyList extends Component {
    render () {
        const { data, onChangeContext } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button onClick={() => onChangeContext("create")}>Ajouter une agence</Button>
                    </div>
                </div>
                <div className="items-table">
                    <div className="items items-default items-agency">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body item-body-image">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Agence</div>
                                        <div className="col-2">Adresse</div>
                                        <div className="col-3">Nombre de biens</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <AgencyItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}