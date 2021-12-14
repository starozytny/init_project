import React, { Component } from 'react';

import { Alert }              from "@dashboardComponents/Tools/Alert";

import { MySessionsItem }     from "./MySessionsItem";

export class MySessionsList extends Component {
    render () {
        const { data } = this.props;

        return <>
            <div>
                <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-3">
                                        <div className="col-1">Intitulé</div>
                                        <div className="col-2">Prix</div>
                                        <div className="col-3 actions">Actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {data && data.length !== 0 ? data.map(elem => {
                            return <MySessionsItem {...this.props} elem={elem} key={elem.id}/>
                        }) : <Alert>Aucun résultat</Alert>}
                    </div>
                </div>
            </div>
        </>
    }
}