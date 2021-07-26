import React, { Component } from 'react';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import Sanitize               from "@dashboardComponents/functions/sanitaze";
import parse                  from "html-react-parser";

export class AgencyRead extends Component {
    render () {
        const { element, onChangeContext } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item">
                        <Button outline={true} icon="left-arrow" type="primary" onClick={() => onChangeContext("list")}>Retour à la liste</Button>
                    </div>
                </div>

                <div className="item-user-read">

                    <div className="user-read-infos">
                        <div className="actions">
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext('update', element)} >Modifier</ButtonIcon>
                        </div>
                        <div className="user-read-infos-container">
                            <div className="avatar">
                                <img src={"/immo/logos/" + element.logo} alt={`Logo de ${element.name}`}/>
                            </div>

                            <div className="main-infos">
                                <div className="name">
                                    <div>#{element.id}</div>
                                    <span>{element.name}</span>
                                </div>
                                <div className="username">
                                    <span>({element.dirname}) <a target="_blank" href={element.website}><span className="icon-link-2" /></a></span>
                                </div>
                                <div className="sub">{parse(element.description)}</div>
                            </div>

                            <div className="footer-infos">
                                <div className="role role-time">Membre depuis le </div>
                                <div className="role">role</div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </>
    }
}