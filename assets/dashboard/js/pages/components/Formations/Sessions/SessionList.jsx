import React, { Component } from 'react';

import Routing        from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Alert }      from "@dashboardComponents/Tools/Alert";
import { Button }     from "@dashboardComponents/Tools/Button";

import { SessionItem }   from "./SessionItem";

export class SessionList extends Component {
    render () {
        const { data, sessionId } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item create">
                        <Button element="a" onClick={Routing.generate('api_registration_enable_attestations', {'session': sessionId})}>
                            Activer toutes les attestations
                        </Button>
                    </div>
                </div>

                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-header-selector" />
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Participant</div>
                                        <div className="col-2">Adhérent</div>
                                        <div className="col-3">Attestation</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <SessionItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}