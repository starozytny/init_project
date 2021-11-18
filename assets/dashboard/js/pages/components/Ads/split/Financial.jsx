import React     from "react";
import Sanitize  from "@commonComponents/functions/sanitaze";

export function Financial({ elem }){

    let content = <>
        <div className="details-tab-infos-main">
            <div>
                <div className="label">Loyer charges comprises</div>
                <div>{Sanitize.toFormatCurrency(elem.financial.price)} / mois</div>
            </div>
            <div>
                <div className="label">Provisions pour charge (Soumis à régularisation annuelle)</div>
                <div>{Sanitize.toFormatCurrency(elem.financial.charges)}</div>
            </div>
            <div>
                <div className="label">Dépôt de garantie</div>
                <div>{Sanitize.toFormatCurrency(elem.financial.deposit)}</div>
            </div>
        </div>
        <div className="details-tab-infos-main">
            <div>
                <div className="label">Honoraire à la charge du locataire avec état des lieux</div>
                <div>{Sanitize.toFormatCurrency(elem.financial.commission)} TTC</div>
            </div>
            <div>
                <div className="label">Etat des lieux</div>
                <div>{Sanitize.toFormatCurrency(elem.financial.partHonoEdl)}</div>
            </div>
        </div>
    </>

    if(elem.typeAd === "Vente"){
        content = <>
            <div className="details-tab-infos-main">
                {elem.financial.bouquet !== null && elem.financial.bouquet !== 0 && <div>
                    <div className="label">Viager</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.bouquet)}</div>
                </div>}
                <div>
                    <div className="label">Prix de vente</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.price)}</div>
                </div>
                {elem.financial.bouquet !== null && elem.financial.bouquet !== 0 && <div>
                    <div className="label">Rente</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.rente)}</div>
                </div>}
                <div>
                    <div className="label">Honoraire à la charge du</div>
                    <div>{elem.financial.honoChargesDe}</div>
                </div>
                <div>
                    <div className="label">Charges mensuelles</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.charges)}</div>
                </div>
                <div>
                    <div className="label">Taxe foncière</div>
                    <div>{Sanitize.toFormatCurrency(elem.financial.foncier)}</div>
                </div>
            </div>
            {elem.copro &&  <div className="details-tab-infos-main">
                <div className="label">En copropriété</div>
                <div>
                    <div className="label">Nombre de lots</div>
                    <div>{Sanitize.toFormatCurrency(elem.copro.nbLot)}</div>
                </div>
                <div>
                    <div className="label">Charges annuelle</div>
                    <div>{elem.financial.chargesAnnuelle}</div>
                </div>
                {elem.corpro.hasProced && <div>
                    <div className="label">Le syndicat des copropriétaires fait l'objet d'une procédure.</div>
                    <div>{Sanitize.toFormatCurrency(elem.copro.detailsProced)}</div>
                </div>}
            </div>}
        </>
    }


    return (<div className="details-tab-infos">
        {content}
    </div>)
}