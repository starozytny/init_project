import React, { Component } from 'react';

import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

import { InfosOrder } from "@dashboardPages/components/Paiement/Order/OrdersItem";

export class LotItem extends Component {
    render () {
        const { elem } = this.props

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <InfosOrder elem={elem} />
                        <div className="col-3">
                            <div className="sub">{elem.createdAtString}</div>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="email">Relancer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}