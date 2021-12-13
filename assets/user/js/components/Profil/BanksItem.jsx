import React, { Component } from 'react';

import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

import Sanitaze         from "@commonComponents/functions/sanitaze";

export class BanksItem extends Component {
    render () {
        const { elem, onDelete, onSwitchMain } = this.props

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-3">
                        <div className="col-1">
                            <div className="name">
                                <span>{Sanitaze.toFormatIbanHidden(elem.iban)}</span>
                            </div>
                            <div className="role" onClick={() => onSwitchMain(elem)}>
                                <span>{elem.isMain ? "Principal" : "Secondaire" }</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.titulaire}</div>
                            <div className="sub">{elem.bic}</div>
                        </div>
                        <div className="col-3 actions">
                            <ButtonIcon icon="pencil" element="a" onClick={Routing.generate('user_bank_update', {'id': elem.id})}>Modifier</ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}