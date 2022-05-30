import React, { Component } from 'react';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class TaxesItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="sub">{elem.code}</div>
                        </div>
                        <div className="col-2">
                            <div className="name">
                                <span>{elem.rate} %</span>
                            </div>
                        </div>
                        <div className="col-3">
                            <span className="sub">{elem.numeroComptable}</span>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                            {!elem.isNatif && <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>}
                            {elem.isNatif && <div className="badge badge-0">Natif</div>}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
