import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

export class InvoicesItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="badges">
                                <div className="badge badge-default">#{elem.numero}</div>
                            </div>
                            <div className="name">
                                <span>{elem.toName}</span>
                            </div>
                            <span className="sub">{elem.toEmail}</span>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.dateAtString}</div>
                        </div>
                        <div className="col-3">
                            <div className="sub">status</div>
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
