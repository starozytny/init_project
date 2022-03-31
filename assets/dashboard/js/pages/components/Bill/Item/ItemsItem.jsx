import React, { Component } from 'react';

import Sanitaze from "@commonComponents/functions/sanitaze";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Selector }   from "@dashboardComponents/Layout/Selector";

export class ItemsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        return <div className="item">
            <Selector id={elem.id} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-6">
                        <div className="col-1">
                            <div className="sub">{elem.reference}</div>
                        </div>
                        <div className="col-2">
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                        </div>
                        <div className="col-3">
                            <span className="sub">{elem.unityString}</span>
                        </div>
                        <div className="col-4">
                            <div className="sub">{elem.rateTva}%</div>
                        </div>
                        <div className="col-5">
                            <div className="sub">{Sanitaze.toFormatCurrency(elem.price)}</div>
                        </div>
                        <div className="col-6 actions">
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
