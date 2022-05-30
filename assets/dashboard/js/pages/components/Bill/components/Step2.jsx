import React from "react";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Checkbox, Input, SelectReactSelectize } from "@dashboardComponents/Tools/Fields";

function ToDefine ({ val }) {
    return val && val !== "" ? val : <span className="txt-danger">A définir</span>
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
                          onSelectSite, sites, site, refCustomer, refSite, enableSite,
                          toName, toAddress, toAddress2, toComplement, toZipcode, toCity, toCountry, toEmail, toPhone1,
                          siName, siAddress, siAddress2, siComplement, siZipcode, siCity, siCountry, siEmail, siPhone1 })
{
    let selectSites = [];
    let selectCustomers = [];
    customers.forEach(it => {
        selectCustomers.push({ value: it.id, label: it.name, identifiant: "cu-" + it.id })
    })

    sites.forEach(si => {
        if(si.customer && si.customer.id === customer){
            selectSites.push({ value: si.id, label: si.name, identifiant: "si-" + si.id })
        }
    })

    let switcherItems = [ { value: 1, label: 'Oui', identifiant: 'oui' } ]

    return <>
        <div className="line line-2">
            <div className="form-group">
                <div className="line line-select-special">
                    <SelectReactSelectize items={selectCustomers} identifiant="customer" placeholder={"Sélectionner un client ?"}
                                          valeur={customer} errors={errors} onChange={(e) => onSelectCustomer(e)}>
                        <span className="icon-user" /> <span>Client</span>
                    </SelectReactSelectize>
                </div>

                <div className="line line-2">
                    <Input valeur={refCustomer} identifiant="refCustomer" errors={errors} onChange={onChange}>* Numéro client</Input>
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
                    <Input identifiant="toZipcode" valeur={toZipcode} errors={errors} onChange={(e) => onChangeZipcodeCity("toCity", e)} type="number">* Code postal</Input>
                    <Input identifiant="toCity" valeur={toCity} errors={errors} onChange={onChange}>* Ville</Input>
                    <Input identifiant="toCountry" valeur={toCountry} errors={errors} onChange={onChange}>Pays</Input>
                </div>
            </div>

            <div className="form-group">
                <div className="line">
                    <Checkbox isSwitcher={true} items={switcherItems} identifiant="enableSite" valeur={enableSite} errors={errors} onChange={onChange}>
                        Associé à un site ?
                    </Checkbox>
                </div>

                {parseInt(enableSite) === 1 && <>
                    <div className="line line-select-special">
                        <SelectReactSelectize items={selectSites} identifiant="site" placeholder={"Sélectionner un site ?"}
                                              valeur={site} errors={errors} onChange={(e) => onSelectSite(e)}>
                            <span className="icon-bookmark" /> <span>Site</span>
                        </SelectReactSelectize>
                    </div>

                    <div className="line line-2">
                        <Input valeur={refSite} identifiant="refSite" errors={errors} onChange={onChange}>* Numéro du site</Input>
                        <Input valeur={siName} identifiant="siName" errors={errors} onChange={onChange}>* Designation du <b><u>site</u></b></Input>
                    </div>

                    <div className="line line-2">
                        <Input valeur={siEmail} identifiant="siEmail" errors={errors} onChange={onChange} type="email">Email</Input>
                        <Input valeur={siPhone1} identifiant="siPhone1" errors={errors} onChange={onChange}>Téléphone</Input>
                    </div>

                    <div className="line">
                        <Input identifiant="siAddress" valeur={siAddress} errors={errors} onChange={onChange}>* Adresse</Input>
                    </div>

                    <div className="line">
                        <Input identifiant="siAddress2" valeur={siAddress2} errors={errors} onChange={onChange}>Adresse ligne 2</Input>
                    </div>
                    <div className="line">
                        <Input identifiant="siComplement" valeur={siComplement} errors={errors} onChange={onChange}>Complément d'adresse</Input>
                    </div>
                    <div className="line line-3">
                        <Input identifiant="siZipcode" valeur={siZipcode} errors={errors} onChange={(e) => onChangeZipcodeCity("siCity", e)} type="number">* Code postal</Input>
                        <Input identifiant="siCity" valeur={siCity} errors={errors} onChange={onChange}>* Ville</Input>
                        <Input identifiant="siCountry" valeur={siCountry} errors={errors} onChange={onChange}>* Pays</Input>
                    </div>
                </>}
            </div>
        </div>


    </>
}