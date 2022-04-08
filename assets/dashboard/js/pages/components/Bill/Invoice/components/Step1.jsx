import React from "react";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { DatePick }   from "@dashboardComponents/Tools/DatePicker";
import { Select }     from "@dashboardComponents/Tools/Fields";

import helper from "@dashboardPages/components/Bill/functions/helper";

export function View1({ dateAt, dueAt })
{
    return <>
        <div className="bloc-edit-icon">
            <ButtonIcon icon="pencil">Modifier</ButtonIcon>
        </div>
        <div className="bloc-edit-content">
            <div><b>Facture n°</b> <span>FAXX-XXXXXX</span></div>
            <div><b>Date de facture :</b> <span>{dateAt ? dateAt.toLocaleDateString() : <span className="txt-danger">A définir</span>}</span></div>
            <div><b>Date d'échéance :</b> <span>{dueAt ? dueAt.toLocaleDateString() : <span className="txt-danger">A définir</span>}</span></div>
        </div>
    </>
}

export function Form1({ dateInvoice, errors, onChange, onChangeDate,
                          dateAt, dueAt, dueType })
{
    let selectDueTypes = helper.getConditionPaiementChoices();

    return <>
        <div className="line">
            <DatePick identifiant="dateAt" valeur={dateAt} minDate={dateInvoice ? new Date(dateInvoice) : ""} errors={errors} onChange={(e) => onChangeDate("dateAt", e)}>
                Date de facture
            </DatePick>
        </div>

        <div className="line">
            <Select items={selectDueTypes} identifiant="dueType" valeur={dueType} noEmpty={true} errors={errors} onChange={onChange}>Conditions de paiement</Select>
        </div>

        {parseInt(dueType) !== 1 && <div className="line">
            <DatePick identifiant="dueAt" valeur={dueAt} minDate={dateAt ? dateAt : new Date()} errors={errors} onChange={(e) => onChangeDate("dueAt", e)}>
                Date d'échéance
            </DatePick>
        </div>}
    </>
}