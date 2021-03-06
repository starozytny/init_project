import React, { Component } from 'react';

import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

export class UserItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete, onSelectors, selectors } = this.props

        let url = Routing.generate('user_homepage', {'_switch_user' : elem.username})

        if(elem.highRoleCode === 2){
            url = Routing.generate('admin_homepage', {'_switch_user' : elem.username})
        }

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} selectors={selectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="avatar">
                        <img src={`https://robohash.org/${elem.username}?size=64x64`} alt={`Avatar de ${elem.username}`}/>
                    </div>
                    <div className="infos">
                        <div>
                            <div className="name">
                                <span>{elem.lastname.toUpperCase()} {elem.firstname}</span>
                                {elem.highRoleCode !== 0 && <span className="role">{elem.highRole}</span>}
                            </div>
                            <div className="sub">{elem.username}</div>
                            {elem.email !== "undefined@undefined.fr" ? <div className="sub">{elem.email}</div> : <div className="sub txt-danger">{elem.email} <span className="icon-warning" /></div>}

                        </div>
                        <div className="actions">
                            {elem.highRoleCode !== 1 &&
                            <>
                                <ButtonIcon icon={"pencil"} onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                                <ButtonIcon icon={"trash"} onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                                <a href={url} target="_blank" className="btn-icon">
                                    <span className="icon-share" />
                                    <span className="tooltip">Imiter</span>
                                </a>
                            </>
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}