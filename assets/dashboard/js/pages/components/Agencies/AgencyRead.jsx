import React, { Component } from 'react';

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import Sanitize               from "@dashboardComponents/functions/sanitaze";
import parse                  from "html-react-parser";
import {ChartAds, ChartBiens} from "@dashboardFolder/js/pages/components/Stats/Charts";

export class AgencyRead extends Component {
    render () {
        const { element, onChangeContext } = this.props;

        let statsAds = null, statsBiens = null;
        let last = element.stats.length;
        if(last > 0){
            statsAds = <ChartAds donnees={JSON.stringify(element.stats)} />;
            statsBiens = <ChartBiens donnees={JSON.stringify([element.stats[last - 1]])} />
        }

        return <>
            <div>
                <div className="toolbar">
                    <div className="item">
                        <Button outline={true} icon="left-arrow" type="primary" onClick={() => onChangeContext("list")}>Retour à la liste</Button>
                    </div>
                </div>

                <div className="item-user-read item-agency-read">

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
                                <div className="sub">{element.description ? parse(element.description) : "Description vide."}</div>
                                <div className="sub sub-contact">
                                    {element.email && <div><u>Email</u> : {element.email}</div>}
                                    {element.emailLocation && <div><u>Email location</u> : {element.emailLocation}</div>}
                                    {element.emailVente && <div><u>Email vente</u> : {element.emailVente}</div>}
                                </div>
                                <div className="sub sub-contact">
                                    {element.phone && <div><u>Téléphone</u> : {Sanitize.toFormatPhone(element.phone)}</div>}
                                    {element.phoneLocation && <div><u>Téléphone location</u> : {Sanitize.toFormatPhone(element.phoneLocation)}</div>}
                                    {element.phoneVente && <div><u>Téléphone vente</u> : {Sanitize.toFormatPhone(element.phoneVente)}</div>}
                                </div>
                                <div className="sub sub-contact">
                                    <div><u>Adresse</u> : {element.address}, {element.zipcode} {element.city}</div>
                                </div>
                                <div className="sub sub-contact"><div>{element.legal ? parse(element.legal) : "Texte légal vide."}</div></div>
                            </div>

                            <div className="footer-infos">
                                <div className="role role-time">
                                    <ButtonIcon target="_blank" element="a" onClick={element.website} icon="link-2">Website</ButtonIcon>
                                    <ButtonIcon target="_blank" element="a" onClick={"/immo/tarifs/" + element.tarif} icon="file">Tarifs</ButtonIcon>
                                </div>
                                <div className="role">{element.dirname}</div>
                            </div>
                        </div>
                    </div>

                    <div className="agency-stats">
                        <div className="title">Annonces immobilières</div>
                        <div className="chart">
                            {statsAds}
                        </div>
                    </div>

                </div>

                <div>
                    {statsBiens}
                </div>
            </div>
        </>
    }
}