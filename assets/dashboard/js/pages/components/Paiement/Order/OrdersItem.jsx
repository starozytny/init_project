import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

import Sanitaze      from "@commonComponents/functions/sanitaze";

export class OrdersItem extends Component {
    render () {
        const { elem, onDelete, onSelectors } = this.props

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                            <div className="sub">{elem.email}</div>
                            <div className="sub">{elem.ibanHidden}</div>
                            <div className="sub">{elem.bic}</div>
                            <div className="sub">{elem.titulaire}</div>
                        </div>
                        <div className="col-2">
                            <div>{Sanitaze.toFormatCurrency(elem.price)}</div>
                            <div className="sub">{elem.participants} pers.</div>
                        </div>

                        <div className="col-3">
                            <div className="role">{elem.statusString}</div>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}