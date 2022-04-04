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

const URL_DUPLICATE_ELEMENT = "api_bill_invoices_duplicate"

export class InvoicesItem extends Component {
    constructor(props) {
        super();

        this.handleDuplicate = this.handleDuplicate.bind(this);
    }

    handleDuplicate = (elem) => {
        Formulaire.loader(true);
        Swal.fire(SwalOptions.options("Dupliquer cette facture ?", "La nouvelle facture sera en mode brouillon."))
            .then((result) => {
                if (result.isConfirmed) {
                    axios.post(Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}), {})
                        .then(function (response) {
                            toastr.info("Facture copiée avec succès.")
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

    render () {
        const { elem, onChangeContext, onDelete } = this.props;

        let dropdownItems = [
            {data: <a href="#" onClick={() => this.handleDuplicate(elem)}>Copier</a>},
        ];

        if(elem.status === STATUS_DRAFT){
            dropdownItems = [...[
                {data: <a href="#" onClick={() => onChangeContext("update", elem)}>Modifier</a>},
                {data: <a href="#" onClick={() => onDelete(elem)}>Supprimer</a>},
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
