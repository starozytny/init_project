import React, { Component } from 'react';

import { Alert }        from "@dashboardComponents/Tools/Alert";

import { LotItem }     from "./LotItem";

export class LotList extends Component {
    render () {
        const { data } = this.props;

        return <>
            <div>
                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Ordre</div>
                                        <div className="col-2">Informations</div>
                                        <div className="col-3">Date</div>
                                        <div className="col-4 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <LotItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}