import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

import Sanitaze         from "@dashboardComponents/functions/sanitaze";

export class EstimationsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors } = this.props

        return <div className="item item-alert item-estimation">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos">
                        <div onClick={() => onChangeContext("read", elem)}>
                            <div className="name">
                                <span>{elem.lastname.toUpperCase()} {elem.firstname}</span>
                            </div>
                            <div className="sub phone">{Sanitaze.toFormatPhone(elem.phone)}</div>
                            <div className="sub address">{elem.zipcode}, {elem.city}</div>
                            <div className="sub role">{elem.typeAd}</div>
                            <div className="sub role">{elem.typeBien}</div>
                            <div className="sub createAt">{elem.createdAtString}, {elem.createdAtAgo}</div>
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