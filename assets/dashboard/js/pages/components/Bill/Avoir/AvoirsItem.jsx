import React, { Component } from 'react';

import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Sanitaze   from "@commonComponents/functions/sanitaze";
import helper     from "../functions/helper"

import { ButtonIcon, ButtonIconDropdown } from "@dashboardComponents/Tools/Button";

const STATUS_DRAFT = 0;
const STATUS_ACTIF = 1;

const URL_DOWNLOAD_INVOICE   = "api_bill_invoices_download";
const URL_DOWNLOAD_ELEMENT   = "api_bill_avoirs_download";
const URL_DUPLICATE_ELEMENT  = "api_bill_avoirs_duplicate";
const URL_SEND_ELEMENT       = "api_bill_avoirs_send";
const URL_ARCHIVE_ELEMENT    = "api_bill_avoirs_archive";

export class AvoirsItem extends Component {
    constructor(props) {
        super();

        this.handleDuplicate = this.handleDuplicate.bind(this);
        this.handleArchive = this.handleArchive.bind(this);
        this.handleSend = this.handleSend.bind(this);
    }

    handleDuplicate = (elem) => {
        helper.confirmAction(this, "create", elem, Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}),
            "Copier cet avoir ?", "L'avoir sera en brouillon.", "Avoir copié avec succès.", 3)
    }

    handleArchive = (elem) => {
        helper.confirmAction(this, "update", elem, Routing.generate(URL_ARCHIVE_ELEMENT, {'id': elem.id}),
            "Archiver cet avoir ?", "", "Avoir archivée avec succès.")
    }

    handleSend = (elem) => {
        helper.confirmAction(this, "update", elem, Routing.generate(URL_SEND_ELEMENT, {'id': elem.id}),
            "Envoyer cet avoir ?", elem.isSent ? "Un mail a déjà été envoyé" : "", "Avoir envoyé avec succès.")
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
                    {data: <div onClick={() => onChangeContext("update", elem)}>Modifier</div>},
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

            if(elem.invoiceId){
                dropdownItems = [...[
                    {data: <a href={Routing.generate(URL_DOWNLOAD_INVOICE, {'id': elem.invoiceId})} target="_blank">Voir la facture</a>},
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
                            <div className="sub">{elem.refInvoice ? "(" + elem.refInvoice + ")" : ""}</div>
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
                            <div className="sub" />
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
