import React, { Component } from 'react';

import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import {ButtonIcon, ButtonIconDropdown} from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

export class SessionItem extends Component {
    render () {
        const { elem, onDelete, onSelectors, onSwitchAttestation } = this.props

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">
                            <div className="name">
                                <span>{elem.worker.lastname} {elem.worker.firstname}</span>
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.user.username}</div>
                        </div>
                        <div className="col-3">
                            <div className="role">
                                <ButtonIcon icon={elem.haveAttestation ? "check" : "disabled"} onClick={() => onSwitchAttestation(elem)}>
                                    {elem.haveAttestation ? "Oui" : "Non"}
                                </ButtonIcon>
                            </div>
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}