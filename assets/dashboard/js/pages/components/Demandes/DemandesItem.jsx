import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

export class DemandesItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors } = this.props

        //
        // have elem.bien.slug
        //

        return <div className="item item-alert item-demande">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos">
                        <div onClick={() => onChangeContext('read', elem)}>
                            <div className="name">
                                {!elem.isSeen && <span className="toSee" />}
                                <span>{elem.name}</span>
                            </div>
                            {elem.email && <div className="sub">{elem.email}</div>}
                            <div className="sub">{elem.phone}</div>
                            <div className="sub createAt">{elem.createdAtAgo}</div>
                        </div>
                        {elem.bien ? <div>
                            <div className="name name-opened">{elem.bien.label}</div>
                            <div>{elem.bien.address.address}</div>
                            <div>{elem.bien.address.shortAddress}</div>
                            <div className="sub">{elem.bien.realRef}</div>
                        </div> : <div>
                            <div className="name name-deleted">Bien supprimé</div>
                            <div>{elem.label}</div>
                            <div>{elem.address}</div>
                            <div>{elem.shortAddress}</div>
                            <div className="sub">{elem.realRef}</div>
                        </div>}

                        <div className="actions">
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

