import React, { Component } from 'react';

import Routing  from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Sanitaze from "@commonComponents/functions/sanitaze";

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";

const STATUS_DRAFT = 0;
const STATUS_TO_PAY = 1;
const STATUS_PAID = 2;
const STATUS_PAID_PARTIAL = 3;
const STATUS_ARCHIVED = 4;

export class InvoicesItem extends Component {
    render () {
        const { elem, onChangeContext, onDelete } = this.props;

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
                            {(elem.status !== STATUS_DRAFT) && <>
                                <ButtonIcon icon="download" element="a" target="_blank" onClick={Routing.generate('api_bill_invoices_download', {'id': elem.id})}>Télécharger</ButtonIcon>
                            </>}
                            {elem.status === STATUS_DRAFT && <>
                                <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                                <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                            </>}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}
