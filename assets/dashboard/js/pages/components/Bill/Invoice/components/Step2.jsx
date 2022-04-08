import React from "react";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Input, SelectReactSelectize } from "@dashboardComponents/Tools/Fields";

function ToDefine ({ val }) {
    return val !== "" ? val : <span className="txt-danger">A définir</span>
}

export function View2({ toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry })
{
    return <>
        {(toName === "" && toAddress === "" && toZipcode === "" && toCity === "") ? <>
            <div className="bloc-edit-icon bloc-edit-add">
                <ButtonIcon icon="add">Ajouter</ButtonIcon>
            </div>
            <div className="bloc-edit-content">
                Ajouter un client
            </div>
        </> : <>
            <div className="bloc-edit-icon">
                <ButtonIcon icon="pencil">Modifier</ButtonIcon>
            </div>
            <div className="bloc-edit-content">
                <div><b>Destinataire</b></div>
                <div>{<ToDefine val={toName} />}</div>
                <div>{<ToDefine val={toAddress} />}</div>
                <div>{toAddress2}</div>
                <div>{toComplement}</div>
                <div>{<ToDefine val={toZipcode} />}, {<ToDefine val={toCity} />}</div>
                {toCountry && <div>{toCountry}</div>}
            </div>
        </>}
    </>
}

export function Form2({ errors, onChange, onChangeZipcodeCity, onSelectCustomer, customers, customer,
                          toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry, toEmail, toPhone1 })
{
    let selectCustomers = [];
    customers.forEach(it => {
        selectCustomers.push({ value: it.id, label: it.name, identifiant: "cu-" + it.id })
    })

    return <>
        <div className="line line-2 line-select-special">
            <SelectReactSelectize items={selectCustomers} identifiant="customer" placeholder={"Sélectionner"}
                                  valeur={customer} errors={errors} onChange={(e) => onSelectCustomer(e)}>
                <span className="icon-group" /> <span>Pré-remplir les informations</span>
            </SelectReactSelectize>
            <Input valeur={toName} identifiant="toName" errors={errors} onChange={onChange}>* Nom / Raison sociale</Input>
        </div>

        <div className="line line-2">
            <Input valeur={toEmail} identifiant="toEmail" errors={errors} onChange={onChange} type="email">Email</Input>
            <Input valeur={toPhone1} identifiant="toPhone1" errors={errors} onChange={onChange}>Téléphone</Input>
        </div>

        <div className="line">
            <Input identifiant="toAddress" valeur={toAddress} errors={errors} onChange={onChange}>* Adresse</Input>
        </div>

        <div className="line">
            <Input identifiant="toAddress2" valeur={toAddress2} errors={errors} onChange={onChange}>Adresse ligne 2</Input>
        </div>
        <div className="line">
            <Input identifiant="toComplement" valeur={toComplement} errors={errors} onChange={onChange}>Complément d'adresse</Input>
        </div>
        <div className="line line-3">
            <Input identifiant="toZipcode" valeur={toZipcode} errors={errors} onChange={onChangeZipcodeCity} type="number">* Code postal</Input>
            <Input identifiant="toCity" valeur={toCity} errors={errors} onChange={onChange}>* Ville</Input>
            <Input identifiant="toCountry" valeur={toCountry} errors={errors} onChange={onChange}>Pays</Input>
        </div>
    </>
}