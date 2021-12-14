import React, { Component } from 'react';

import axios            from "axios";
import Routing          from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon, ButtonIconDropdown } from "@dashboardComponents/Tools/Button";
import { Selector }     from "@dashboardComponents/Layout/Selector";

import Sanitaze         from "@commonComponents/functions/sanitaze";
import Formulaire       from "@dashboardComponents/functions/Formulaire";

export class SessionsItem extends Component {
    constructor(props) {
        super();

        this.handleDuplicate = this.handleDuplicate.bind(this);
    }

    handleDuplicate = (elem) => {
        Formulaire.loader(true);
        let self = this;
        axios({ method: "POST", url: Routing.generate('api_sessions_duplicate', {'id': elem.id}), data: [] })
            .then(function (response) {
                let data = response.data;
                location.reload();
            })
            .catch(function (error) {
                Formulaire.displayErrors(self, error);
            })
            .then(() => {
                Formulaire.loader(false);
            })
        ;
    }

    render () {
        const { elem, onChangeContext, onDelete, onSelectors, onSwitchPublished } = this.props

        let actions = [
            {data: <ButtonIcon element="a" icon="download" target="_blank"
                               onClick={Routing.generate('api_sessions_emargements', {'slug': elem.slug})}
                               text="Emargements" />},
            {data: <ButtonIcon element="a" icon="user"
                               onClick={Routing.generate('admin_sessions_read', {'slug': elem.slug})}
                               text="Participants" />},
            {data: <ButtonIcon icon="file"
                               onClick={() => this.handleDuplicate(elem)}
                               text="Dupliquer" />},
        ];

        return <div className="item">
            <Selector id={elem.id} onSelectors={onSelectors} />

            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <InfosSession elem={elem} showFormation={false} admin={true}/>
                        <div className="col-4 actions">
                            <ButtonIcon icon={elem.isPublished ? "vision" : "vision-not"} onClick={() => onSwitchPublished(elem)}>
                                {elem.isPublished ? "En ligne" : "Hors ligne"}
                            </ButtonIcon>
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                            <ButtonIconDropdown icon="menu" items={actions}>Autres</ButtonIconDropdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export function HeaderSession({ haveSelector = false }) {
    return <>
        <div className="item item-header">
            {haveSelector && <div className="item-header-selector" /> }
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-4">
                        <div className="col-1">Session</div>
                        <div className="col-2">Informations</div>
                        <div className="col-3">Participants</div>
                        <div className="col-4 actions">Actions</div>
                    </div>
                </div>
            </div>
        </div>
    </>
}

export function InfosSession({ elem, showFormation = true, admin = false }) {

    let participants = elem.registrations.length + " / " + elem.max + " pers.";

    return <>
        <div className="col-1">
            <div className="name">
                <span>{admin && "#" + elem.id + " : "} {elem.fullDate}</span>
            </div>
            {showFormation && <div className="sub">{elem.formation.name}</div>}
            <div className="sub">{elem.animator}</div>
            {admin && <div className="sub">{elem.slug}</div>}
        </div>
        <div className="col-2">
            <div className="sub">{elem.time} {elem.time && elem.time2 ? " - " : ""} {elem.time2}</div>
            <div className="sub">{Sanitaze.toFormatCurrency(elem.priceTTC)} TTC / unité</div>
        </div>
        <div className="col-3">
            <div className="sub">
                {admin ? <a href={Routing.generate('admin_sessions_read', {'slug': elem.slug})}>{participants}</a> : participants}
            </div>
        </div>
    </>
}