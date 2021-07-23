import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

export class AgencyItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props

        return <div className="item">
            <div className="item-content">
                <div className="item-body item-body-image">
                    <div className="item-image">
                        <img src={`/immo/logos/${elem.logo}`} alt={`Image de ${elem.name}`}/>
                    </div>
                    <div className="infos infos-col-3">
                        <div className="col-1">
                            <div className="name">
                                <span>{elem.name}</span>
                                <a target="_blank" href={elem.website}><span className="icon-link-2" /></a>
                            </div>
                            <div className="sub">{elem.dirname}</div>
                            <div className="sub">{elem.email}</div>
                            <div className="sub">{elem.phone}</div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.address}, {elem.zipcode} {elem.city}</div>
                        </div>
                        <div className="col-3 actions">
                            <ButtonIcon icon="pencil">Modifier</ButtonIcon>
                            <ButtonIcon icon="trash">Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

