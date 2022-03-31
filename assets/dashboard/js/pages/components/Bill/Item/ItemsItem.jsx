import React, { Component } from 'react';

import Routing  from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Sanitaze from "@commonComponents/functions/sanitaze";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class ItemsItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="sub">{elem.reference}</div>
                        </div>
                        <div className="col-2">
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                            <span className="sub">{elem.unity}</span>
                        </div>
                        <div className="col-3">
                            <div className="sub">{Sanitaze.toFormatCurrency(elem.price)}</div>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
