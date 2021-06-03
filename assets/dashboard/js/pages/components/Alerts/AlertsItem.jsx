import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

export class AlertsItem extends Component {
    render () {
        const { elem, onDelete, onSelectors } = this.props

        return <div className="item item-alert">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos">
                        <div>
                            <div className="name">
                                <span>{elem.email}</span>
                            </div>
                            <div className="sub role">{elem.typeAd}</div>
                            <div className="sub role">{elem.typeBiensString}</div>
                            <div className="sub createAt">{elem.createdAtAgo}</div>
                        </div>
                        <div className="actions">
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}