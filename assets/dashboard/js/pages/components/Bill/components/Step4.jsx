import React from "react";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { DatePick }   from "@dashboardComponents/Tools/DatePicker";
import { Select }     from "@dashboardComponents/Tools/Fields";

import helper from "@dashboardPages/components/Bill/functions/helper";

export function View4({ page, dueAt, dueType, valideTo, payType })
{
    let selectPayTypes = helper.getModePaiementChoices();

    let payTypeString = null;
    selectPayTypes.forEach(it => {
        if(it.value === parseInt(payType)){
            payTypeString = it.label;
        }
    })

    return <>
        <div className="bloc-edit-icon">
            <ButtonIcon icon="pencil">Modifier</ButtonIcon>
        </div>
        <div className="bloc-edit-content">
            {page === "invoice" ? <>
                {parseInt(dueType) !== 1 ? <>
                    <div><b>Date d'échéance :</b> <span>{dueAt ? dueAt.toLocaleDateString() : <span className="txt-danger">A définir</span>}</span></div>
                </>: <>
                    <div><b>Date d'échéance :</b> <span>Acquittée</span></div>
                </>}
                <div><b>Mode de règlement :</b> <span>{payTypeString ? payTypeString : <span className="txt-danger">A définir</span>}</span></div>
            </> : (page === "quotation" ? <>
                <div><b>Date de validité :</b> <span>{valideTo ? valideTo.toLocaleDateString() : <span className="txt-danger">A définir</span>}</span></div>
            </> : null)}

        </div>
    </>
}

export function Form4({ page, errors, onChange, onChangeDate,
                          dateAt, dueAt, dueType, valideTo, payType })
{
    let selectDueTypes = helper.getConditionPaiementChoices();
    let selectPayTypes = helper.getModePaiementChoices();

    return <>
        {page === "invoice" && <>
            <div className="line">
                <Select items={selectDueTypes} identifiant="dueType" valeur={dueType} noEmpty={true} errors={errors} onChange={onChange}>Conditions de paiement</Select>
            </div>

            {parseInt(dueType) !== 1 && <div className="line">
                <DatePick identifiant="dueAt" valeur={dueAt} minDate={dateAt ? dateAt : new Date()} errors={errors} onChange={(e) => onChangeDate("dueAt", e)}>
                    Date d'échéance
                </DatePick>
            </div>}

            <div className="line">
                <Select items={selectPayTypes} identifiant="payType" valeur={payType} noEmpty={true} errors={errors} onChange={onChange}>Mode de règlement</Select>
            </div>
        </>}

        {page === "quotation" && <>
            <div className="line">
                <DatePick identifiant="valideTo" valeur={valideTo} minDate={dateAt ? dateAt : new Date()} errors={errors} onChange={(e) => onChangeDate("valideTo", e)}>
                    Date de validité
                </DatePick>
            </div>
        </>}
    </>
}