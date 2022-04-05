import React, { Component } from 'react';

import axios             from "axios";
import toastr            from "toastr";
import Swal              from "sweetalert2";
import SwalOptions       from "@commonComponents/functions/swalOptions";
import Routing           from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Sanitaze   from "@commonComponents/functions/sanitaze";
import Formulaire from "@dashboardComponents/functions/Formulaire";

import { ButtonIcon, ButtonIconDropdown } from "@dashboardComponents/Tools/Button";

const STATUS_DRAFT = 0;
const STATUS_TO_PAY = 1;
const STATUS_PAID = 2;
const STATUS_PAID_PARTIAL = 3;
const STATUS_ARCHIVED = 4;

const URL_DUPLICATE_ELEMENT = "api_bill_invoices_duplicate";
const URL_FINAL_ELEMENT     = "api_bill_invoices_final";

const TEXT_GENERATE = "En cas d'erreur sur la facture " +
    "il faudra faire un avoir pour la rectifier. <br><br>" +
    "<b>Un mail sera envoyé aux adhérents pour les notifier de la création de leur(s) facture(s)</b>"


function confirmAction (elem, url, title, text, messageSuccess) {
    Formulaire.loader(true);
    Swal.fire(SwalOptions.options(title, text))
        .then((result) => {
            if (result.isConfirmed) {
                axios.post(url, {})
                    .then(function (response) {
                        toastr.info(messageSuccess)
                        setTimeout(() => {
                            location.reload()
                        }, 2000)
                    })
                    .catch(function (error) {
                        Formulaire.loader(false);
                        Formulaire.displayErrors(self, error, "Une erreur est survenue, veuillez contacter le support.")
                    })
                ;
            }
        })
    ;
}

export class InvoicesItem extends Component {
    constructor(props) {
        super();

        this.handleDuplicate = this.handleDuplicate.bind(this);
        this.handleFinal = this.handleFinal.bind(this);
    }

    handleFinal = (elem) => {
        confirmAction(elem, Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}),
            "Dupliquer cette facture ?", "La nouvelle facture sera en mode brouillon.", "Facture copiée avec succès.")
    }

    handleDuplicate = (elem) => {
        confirmAction(elem, Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}),
            "Finaliser cette facture ?", "Une fois finalisée, la facture <u>ne pourra plus être modifiée</u>." + TEXT_GENERATE, "Facture finalisée avec succès.")
    }

    render () {
        const { elem, onChangeContext, onDelete, onGenerate } = this.props;

        let dropdownItems = [
            {data: <a href="#" onClick={() => this.handleDuplicate(elem)}>Copier</a>},
        ];

        if(elem.status === STATUS_DRAFT){
            dropdownItems = [...[
                {data: <div onClick={() => onChangeContext("update", elem)}>Modifier</div>},
                {data: <div onClick={() => onDelete(elem)}>Supprimer</div>},
                {data: <div className="dropdown-separator" />},
                {data: <div onClick={() => onGenerate(elem)}>Finaliser</div>},
                {data: <div className="dropdown-separator" />},
            ], ...dropdownItems]
        }

        if(elem.status !== STATUS_DRAFT && elem.status !== STATUS_ARCHIVED){
            dropdownItems = [...[
                {data: <a href="/">Archiver</a>}
            ], ...dropdownItems]
        }

        if(elem.status === STATUS_TO_PAY){
            dropdownItems = [...[
                {data: <a href="/">Envoyer</a>},
                {data: <a href="/">Entrer un paiement</a>},
                {data: <div className="dropdown-separator" />},
            ], ...dropdownItems]
        }


        return <div className="item">
            <div className="item-content">
                <div className="item-body">
                    <div className="infos infos-col-7">
                        <div className="col-1">
                            <div className="sub">{elem.numero}</div>
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
                            <div className="sub">{elem.dueAtString}</div>
                        </div>
                        <div className="col-5">
                            <div className="sub">{Sanitaze.toFormatCurrency(elem.totalTtc)}</div>
                        </div>
                        <div className="col-6">
                            <div className={"badge badge-" + elem.status}>{elem.statusString}</div>
                        </div>
                        <div className="col-7 actions">
                            <ButtonIcon icon="download" element="a" target="_blank" onClick={Routing.generate('api_bill_invoices_download', {'id': elem.id})}>Télécharger</ButtonIcon>
                            <ButtonIconDropdown type="default" icon="more" items={dropdownItems} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
