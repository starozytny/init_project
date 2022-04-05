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

const URL_DUPLICATE_ELEMENT  = "api_bill_invoices_duplicate";
const URL_ARCHIVE_ELEMENT    = "api_bill_invoices_archive";

function confirmAction (self, context, elem, url, title, text, messageSuccess) {
    Swal.fire(SwalOptions.options(title, text))
        .then((result) => {
            if (result.isConfirmed) {
                Formulaire.loader(true);

                axios.post(url, {})
                    .then(function (response) {
                        let data = response.data;

                        toastr.info(messageSuccess)

                        if (self.props.onUpdateList) {
                            Formulaire.loader(false);
                            self.props.onUpdateList(data, context);
                        }else{
                            setTimeout(() => {
                                location.reload()
                            }, 2000)
                        }
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
        this.handleArchive = this.handleArchive.bind(this);
    }

    handleDuplicate = (elem) => {
        confirmAction(this, "create", elem, Routing.generate(URL_DUPLICATE_ELEMENT, {'id': elem.id}),
            "Dupliquer cette facture ?", "La nouvelle facture sera en mode brouillon.", "Facture copiée avec succès.")
    }

    handleArchive = (elem) => {
        confirmAction(this, "update", elem, Routing.generate(URL_ARCHIVE_ELEMENT, {'id': elem.id}),
            "Archiver cette facture ?", "", "Facture archivée avec succès.")
    }

    render () {
        const { elem, onChangeContext, onDelete, onGenerate, onPayement } = this.props;

        let dropdownItems = [
            {data: <div onClick={() => this.handleDuplicate(elem)}>Copier</div>},
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

            if(elem.status === STATUS_TO_PAY){
                dropdownItems = [...[
                    {data: <div onClick={() => onPayement(elem)}>Entrer un paiement</div>},
                    {data: <div className="dropdown-separator" />},
                ], ...dropdownItems]
            }
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
                            <div className="badges">
                                <div className={"badge badge-" + elem.status}>{elem.statusString}</div>
                            </div>
                            {elem.isArchived && <div className="badge badge-default">Archivé</div>}
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
