import React from "react";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { DatePick }   from "@dashboardComponents/Tools/DatePicker";

import helper from "@dashboardPages/components/Bill/functions/helper";

export function View1({ page, context, society, numero, dateAt })
{
    let preposition, name, prefix, counter, year;

    switch (page){
        case "avoir":
            preposition = "de l'"
            name = "Avoir";
            prefix = "AV";
            year = society.yearAvoir;
            counter = society.counterAvoir;
            break;
        case "quotation":
            preposition = "du"
            name = "Devis";
            prefix = "DE";
            year = society.yearQuotation;
            counter = society.counterQuotation;
            break;
        default:
            preposition = "de"
            name = "Facture";
            prefix = "FA";
            year = society.yearInvoice;
            counter = society.counterInvoice;
            break;
    }

    let nYear = dateAt ? dateAt.getFullYear() : year;
    let numeroGuessed = helper.guessNumero(prefix, year, nYear, counter);

    return <>
        <div className="bloc-edit-icon">
            <ButtonIcon icon="pencil">Modifier</ButtonIcon>
        </div>
        <div className="bloc-edit-content">
            <div><b>{name} n°</b> <span>{context === "update" ? numero : "Brouillon"}</span></div>
            <div><b>Date {preposition} {name.toLowerCase()} :</b> <span>{dateAt ? dateAt.toLocaleDateString() : <span className="txt-danger">A définir</span>}</span></div>
        </div>
    </>
}

export function Form1({ page, dateLimit, errors, onChangeDate, dateAt })
{
    return <>
        <div className="line">
            <DatePick identifiant="dateAt" valeur={dateAt} minDate={dateLimit ? dateLimit : ""} errors={errors} onChange={(e) => onChangeDate("dateAt", e)}>
                Date de {page === "invoice" ? 'facture' : (page === "quotation" ? 'devis' : "avoir")}
            </DatePick>
        </div>
    </>
}