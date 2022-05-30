import React, { Component } from 'react';

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Selector }   from "@dashboardComponents/Layout/Selector";

export class SitesItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        return <div className="item">
            <Selector id={elem.id} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="sub">{elem.numero}</div>
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.address}</div>
                            <div className="sub">{elem.address2}</div>
                            <div className="sub">{elem.complement}</div>
                            <div className="sub">{elem.zipcode}, {elem.city}</div>
                        </div>
                        <div className="col-3">
                            <div className="sub">{elem.customer ? elem.customer.name : <span className="txt-danger">Aucun client</span>}</div>
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

export class SitesItemCustomerForm extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onDelink } = this.props;

        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-3">
                        <div className="col-1">
                            <div className="sub">{elem.numero}</div>
                            <div className="name">
                                <span>{elem.name}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.address}</div>
                            <div className="sub">{elem.address2}</div>
                            <div className="sub">{elem.complement}</div>
                            <div className="sub">{elem.zipcode}, {elem.city}</div>
                        </div>
                        <div className="col-3 actions">
                            {/*<ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>*/}
                            {/*<ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>*/}
                            <ButtonIcon icon="user-delete" onClick={() => onDelink(elem)}>Dissocier</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}