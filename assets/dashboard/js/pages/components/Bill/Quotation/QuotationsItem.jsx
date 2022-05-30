import React, { Component } from 'react';

import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Sanitaze   from "@commonComponents/functions/sanitaze";
import helper     from "../functions/helper"

import { ButtonIcon, ButtonIconDropdown } from "@dashboardComponents/Tools/Button";

const STATUS_DRAFT = 0;
const STATUS_PROCESSING = 1;
const STATUS_ACCEPTED = 2;
const STATUS_REFUSED = 3;

const URL_DOWNLOAD_INVOICE   = "api_bill_invoices_download";
const URL_CREATE_INVOICE     = "admin_bill_quotations_create_invoice";
const URL_ANSWER_ELEMENT     = "api_bill_quotations_answer";
const URL_DOWNLOAD_ELEMENT   = "api_bill_quotations_download";
const URL_DUPLICATE_ELEMENT  = "api_bill_quotations_duplicate";
const URL_SEND_ELEMENT       = "api_bill_quotations_send";
const URL_ARCHIVE_ELEMENT    = "api_bill_quotations_archive";

export class QuotationsItem extends Component {
    constructor(props) {
        super();

        this.handleDuplicate = this.handleDuplicate.bind(this);
        this.handleArchive = this.handleArchive.bind(this);
        this.handleSend = this.handleSend.bind(this);
        this.handleAnswer = this.handleAnswer.bind(this);
    }

    handleDuplicate = (elem) => {
        helper.confirmAction(this, "create", elem, Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}),
            "Copier ce devis ?", "Le devis sera en brouillon.", "Devis copié avec succès.", 3)
    }

    handleArchive = (elem) => {
        helper.confirmAction(this, "update", elem, Routing.generate(URL_ARCHIVE_ELEMENT, {'id': elem.id}),
            "Archiver ce devis ?", "", "Devis archivé avec succès.")
    }

    handleSend = (elem) => {
        helper.confirmAction(this, "update", elem, Routing.generate(URL_SEND_ELEMENT, {'id': elem.id}),
            "Envoyer ce devis ?", elem.isSent ? "Un mail a déjà été envoyé" : "", "Devis envoyé avec succès.")
    }

    handleAnswer = (elem, answer) => {
        helper.callApiGenerique(this, "update", elem, Routing.generate(URL_ANSWER_ELEMENT, {'id': elem.id, 'answer': answer}),
            "", "", "Devis mis à jour.")
    }

    render () {
        const { elem, onChangeContext, onDelete, onGenerate } = this.props;

        let dropdownItems = [
            {data: <div onClick={() => this.handleDuplicate(elem)}>Copier</div>},
            {data: <div onClick={() => this.handleSend(elem)}>Envoyer</div>},
        ];

        if(!elem.isArchived){
            if(elem.status === STATUS_DRAFT){
                dropdownItems = [...[
                    {data: <div onClick={() => onDelete(elem)}>Supprimer</div>},
                    {data: <div className="dropdown-separator" />},
                    {data: <div onClick={() => onGenerate(elem)}>Finaliser</div>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }

            if(elem.status !== STATUS_DRAFT){
                dropdownItems = [...[
                    {data: <div onClick={() => this.handleArchive(elem)}>Archiver</div>}
                ], ...dropdownItems]
            }

            if(elem.status === STATUS_PROCESSING){
                dropdownItems = [...[
                    {data: <div onClick={() => this.handleAnswer(elem, 1)}>Accepter</div>},
                    {data: <div onClick={() => this.handleAnswer(elem, 0)}>Refuser</div>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }

            if(!elem.invoiceId && elem.status === STATUS_ACCEPTED){
                dropdownItems = [...[
                    {data: <a href={Routing.generate(URL_CREATE_INVOICE, {'id': elem.id})}>Créer une facture</a>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }

            if(!elem.invoiceId && (elem.status === STATUS_ACCEPTED || elem.status === STATUS_REFUSED)){
                dropdownItems = [...[
                    {data: <div onClick={() => this.handleAnswer(elem, 2)}>Annuler la réponse</div>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }

            if(elem.invoiceId){
                dropdownItems = [...[
                    {data: <a href={Routing.generate(URL_DOWNLOAD_INVOICE, {'id': elem.invoiceId})} target="_blank">Voir la facture</a>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }

            if(!elem.invoiceId && elem.status !== STATUS_ACCEPTED && elem.status !== STATUS_REFUSED){
                dropdownItems = [...[
                    {data: <div onClick={() => onChangeContext("update", elem)}>Modifier</div>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }
        }


        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-7">
                        <div className="col-1">
                            <div className="sub">{elem.status === STATUS_DRAFT ? "-" : elem.numero}</div>
                        </div>
                        <div className="col-2">
                            <div className="name">
                                <span>{elem.toName}</span>
                            </div>
                            <span className="sub">{elem.toEmail}</span>
                        </div>
                        <div className="col-3">
                            <div className="sub">{elem.dateAtString}</div>
                        </div>
                        <div className="col-4">
                            <div className="sub">{elem.valideToString}</div>
                        </div>
                        <div className="col-5">
                            <div className="name">{Sanitaze.toFormatCurrency(elem.totalTtc)}</div>
                        </div>
                        <div className="col-6">
                            <div className="badges">
                                <div className={"badge badge-" + elem.status}>{elem.statusString}</div>
                            </div>
                            {elem.isArchived && <div className="badge badge-default">Archivé</div>}
                        </div>
                        <div className="col-7 actions">
                            <ButtonIcon icon="file" element="a" target="_blank" onClick={Routing.generate(URL_DOWNLOAD_ELEMENT, {'id': elem.id})}>Télécharger</ButtonIcon>
                            <ButtonIconDropdown type="default" icon="more" items={dropdownItems} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
