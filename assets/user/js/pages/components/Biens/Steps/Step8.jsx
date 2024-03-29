import React from "react";

import { Input, SelectReactSelectize } from "@dashboardComponents/Tools/Fields";
import { DatePick }     from "@dashboardComponents/Tools/DatePicker";
import { Button }       from "@dashboardComponents/Tools/Button";
import { UtContact }    from "@dashboardComponents/Tools/Utilitaire";
import { FormActions }  from "@userPages/components/Biens/Form/Form";

import helper   from "@userPages/components/Biens/helper";

import {
    OwnerMainInfos,
} from "@dashboardPages/components/Immo/Owners/OwnersItem";
import { NegotiatorBubble } from "@dashboardPages/components/Immo/Negociators/NegotiatorsItem";

const CURRENT_STEP = 8;

export function Step8({ step, errors, onNext, onDraft, onChange, onChangeSelect, onChangeDate, onOpenAside,
                          allOwners, owner, inform, lastname, phone1, email, visiteAt, keysNumber, keysWhere })
{
    let itemOwner = null;
    if(owner){
        allOwners.forEach(ow => {
            if(ow.id === owner){
                itemOwner = ow;
            }
        })
    }

    let informItems = helper.getItems("informs")

    return <div className={"step-section" + (step === CURRENT_STEP ? " active" : "")}>
        <div className="line special-line contact-line">
            <div className="form-group">
                <label>Propriétaire</label>

                <Button type="default" onClick={() => onOpenAside("owner-select")}>Sélectionner/ajouter un propriétaire</Button>

                {itemOwner && <div className="items-table">
                    <div className="items items-default">
                        <div className="item item-header">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">Propriétaire</div>
                                        <div className="col-2">Contact</div>
                                        <div className="col-3">Négociateur</div>
                                        <div className="col-4 actions" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="item">
                            <div className="item-content">
                                <div className="item-body">
                                    <div className="infos infos-col-4">
                                        <div className="col-1">
                                            <OwnerMainInfos elem={itemOwner} />
                                        </div>

                                        <div className="col-2">
                                            <UtContact elem={itemOwner} />
                                        </div>

                                        <div className="col-3">
                                            <NegotiatorBubble elem={itemOwner.negotiator} />
                                        </div>
                                        <div className="col-4 actions">
                                            <div className="sub">Sélectionné</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>}
            </div>
        </div>

        <div className="line special-line">
            <div className="form-group">
                <label>Contact</label>
            </div>
            <div className="line line-3">
                <div className="form-group" />
                <SelectReactSelectize items={informItems} identifiant="inform" valeur={inform} errors={errors}
                                      onChange={(e) => onChangeSelect('inform', e)}>
                    Qui prévenir ?
                </SelectReactSelectize>
                <div className="form-group" />
            </div>
            {inform === 3 && <div className="line line-3">
                <Input identifiant="lastname" valeur={lastname} errors={errors} onChange={onChange}>
                    <span>Nom</span>
                </Input>
                <Input identifiant="phone1" valeur={phone1} errors={errors} onChange={onChange} type="number">
                    <span>Téléphone</span>
                </Input>
                <Input identifiant="email" valeur={email} errors={errors} onChange={onChange} type="email">
                    <span>Email</span>
                </Input>
            </div>}
        </div>
        <div className="line special-line">
            <div className="line line-2">
                <div className="form-group">
                    <div className="line line-2">
                        <DatePick identifiant="visiteAt" valeur={visiteAt} errors={errors}
                                  onChange={(e) => onChangeDate("visiteAt", e)}>
                            Visite à partir de quelle date
                        </DatePick>
                        <div className="form-group" />
                    </div>
                </div>

                <div className="form-group" />
            </div>
            <div className="line line-2">
                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={keysNumber} identifiant="keysNumber" errors={errors} onChange={onChange} type="number">
                            Nombre de clés
                        </Input>
                        <Input valeur={keysWhere} identifiant="keysWhere" errors={errors} onChange={onChange}>
                            Où trouver les clés
                        </Input>
                    </div>
                </div>

                <div className="form-group" />
            </div>
        </div>

        <FormActions onNext={onNext} onDraft={onDraft} currentStep={CURRENT_STEP} />
    </div>
}