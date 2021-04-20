import React     from "react";
import Sanitize  from "@dashboardComponents/functions/sanitaze";
import parseHtml from "html-react-parser";

export function Infos({ elem }){
    return (<div className="details-tab-infos">
        <div className="details-tab-infos-main">
            <div>
                <div className="label">Identifiant immuable</div>
                <div>{elem.identifiant}</div>
            </div>
            <div>
                <div className="label">Référence transfert</div>
                <div>{elem.ref}</div>
            </div>
            <div>
                <div className="label">Référence agence</div>
                <div>{elem.realRef}</div>
            </div>
            {elem.typeAd === "Location" && elem.feature.isMeuble !== 3 && <div>
                <div className="label">Bien meublé</div>
                <div>{Sanitize.getTrilieanResponse(elem.feature.isMeuble)}</div>
            </div>}
            {elem.dispoString && <div>
                <div className="label">Disponibilité</div>
                <div>{elem.dispoString}</div>
            </div>}
        </div>

        <div className="details-tab-infos-content">
            <div className="content">
                <div className="label">Description du bien immobilier</div>
                <p>{parseHtml(elem.content)}</p>
            </div>
            <div className="contacts">
                <div className="label">Contacts</div>
                <div className="contact">
                    <div>{elem.agency.name}</div>
                    <div>{Sanitize.toFormatPhone(elem.agency.phone)}</div>
                    <div>{elem.agency.email}</div>
                </div>
                {elem.responsable && <div className="contact">
                    <div>{elem.responsable.name}</div>
                    <div>{Sanitize.toFormatPhone(elem.responsable.phone)}</div>
                    <div>{elem.responsable.email}</div>
                </div>}
            </div>
        </div>
    </div>)
}