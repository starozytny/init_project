import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";
import Sanitaze         from "@dashboardComponents/functions/sanitaze";

export class FormationsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors, onSwitchPublished } = this.props

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-3">
                        <div className="col-1" onClick={() => onChangeContext('read', elem)}>
                            <div className="name">
                                {!elem.isSeen && <span className="toSee" />}
                                <span>{elem.name}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{Sanitaze.toFormatCurrency(elem.price)}</div>
                        </div>
                        <div className="col-3 actions">
                            <ButtonIcon icon={elem.isPublished ? "vision" : "vision-not"} onClick={() => onSwitchPublished(elem)}>
                                {elem.isPublished ? "En ligne" : "Hors ligne"}
                            </ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}