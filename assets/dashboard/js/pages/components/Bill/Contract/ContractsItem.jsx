import React, { Component } from 'react';

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { ButtonIcon, ButtonIconDropdown } from "@dashboardComponents/Tools/Button";

import Sanitaze from "@commonComponents/functions/sanitaze";
import helper   from "@dashboardPages/components/Bill/functions/helper";

import { NumeroRelationForm } from "@dashboardPages/components/Bill/Contract/ContractForm";

const URL_ACTIVE_ELEMENT    = "api_bill_contracts_relation_active";
const URL_PREVIEW_ELEMENT   = "api_bill_contracts_relation_preview";
const URL_UPDATE_INVOICE    = "api_bill_contracts_invoice_update";
const URL_DOWNLOAD_INVOICE  = "api_bill_invoices_download";

const STATUS_DRAFT = 0;
const STATUS_TO_PAY = 1;
const STATUS_PAID = 2;
const STATUS_PAID_PARTIAL = 3;

function relationActive (self, elem) {
    let title = elem.isActive ? "Désactiver" : "Activer";

    helper.confirmAction(self, "update", elem, Routing.generate(URL_ACTIVE_ELEMENT, {'id': elem.relationId}),
        title + " ce client ?", "", "Mise à jours avec succès.", 2)
}

function getCustomers (elem, relations) {
    let customers = [];
    relations.forEach(re => {
        if(re.contract.id === elem.id){
            re.customer.relationNumero = re.numero;
            re.customer.relationId = re.id;
            re.customer.isActive = re.isActive;
            re.customer.lastYear = re.lastYear;
            re.customer.lastMonth = re.lastMonth;
            re.customer.site = re.site;
            customers.push(re.customer);
        }
    })

    return customers;
}

export class ContractsItem extends Component {
    constructor(props) {
        super(props);

        this.handleRelationActive = this.handleRelationActive.bind(this);
    }

    handleRelationActive = (elem) => { relationActive(this, elem) }

    render () {
        const { elem, relations, onChangeContext, onDelete, onOpenAside, onChangeRelation, onUpdateRelation } = this.props;

        let customers = getCustomers(elem, relations)

        let isUnderline = !elem.isActive;

        return <>
            <div className="item item-main">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-4">
                            <div className="col-1">
                                <div className={"name" + (isUnderline ? " linethrough txt-danger" : "")}>
                                    <span><span className="sub">#{elem.numero} -</span> {elem.name}</span>
                                </div>
                            </div>
                            <div className="col-2">
                                <div className={"badge badge-" + elem.period}>{elem.periodString}</div>
                            </div>
                            <div className="col-3">
                                <div className="sub">{Sanitaze.toFormatCurrency(elem.totalTtc)}</div>
                            </div>
                            <div className="col-4 actions">
                                {!isUnderline && <>
                                    <ButtonIcon icon="user-add" onClick={() => onOpenAside(elem)} tooltipWidth={90}>Ajouter un client</ButtonIcon>
                                    <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                                    <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                                </>}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {customers.length !== 0 ? customers.map((customer, index) => {

                let isUnderline = !customer.isActive;

                let dropdownItems = [
                    {data: <div onClick={() => this.handleRelationActive(customer)}>{customer.isActive ? "Désactiver" : "Activer"} ce client</div>},
                ];

                return <div className="item item-sub" key={index}>
                    <div className="item-content">
                        <div className="item-body">
                            <div className="infos infos-col-4">
                                <div className="col-1">
                                    <div className={"sub indent-24" + (isUnderline ? " linethrough" : "") + (customer.relationNumero ? "" : " txt-danger") }>
                                        {customer.name} {customer.site ? " - S.D.C " + customer.site.name : ""}
                                    </div>
                                </div>
                                <div className="col-2">
                                    <div className="sub">
                                        {customer.relationNumero ? "#" + customer.relationNumero : <span className="txt-danger">Entrez un numéro de contrat</span>}
                                    </div>
                                </div>
                                <div className="col-3">
                                    <div className="sub contract-numero-form">
                                        {!customer.relationNumero && <NumeroRelationForm element={customer} numero={customer.relationNumero} onUpdateList={onUpdateRelation} />}
                                    </div>
                                </div>
                                <div className="col-4 actions">
                                    {customer.site ? <>
                                        <ButtonIcon icon="cancel" onClick={() => onChangeRelation("site", elem, customer.site)} tooltipWidth={90}>Enlever ce site</ButtonIcon>
                                    </> : <>
                                        <ButtonIcon icon="cancel" onClick={() => onChangeRelation("customer", elem, customer)} tooltipWidth={90}>Enlever ce client</ButtonIcon>
                                    </>}
                                    <ButtonIconDropdown type="default" icon="more" items={dropdownItems} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            }) : <div className="item item-sub">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-2">
                            <div className="col-1">
                                <div className="sub indent-24 txt-danger">Aucun client lié à ce contrat.</div>
                            </div>
                            <div className="col-2 actions" />
                        </div>
                    </div>
                </div>
            </div>}
        </>
    }
}

export class ContractsItemProcess extends Component {
    constructor(props) {
        super(props);

        this.handleRelationActive = this.handleRelationActive.bind(this);
    }

    handleRelationActive = (elem) => { relationActive(this, elem) }

    render () {
        const { elem, relations, invoices, enableItemAction, year, month, onGenerate } = this.props;

        let customers = getCustomers(elem, relations)
        let invoicesRelated = [];
        invoices.forEach(inv => {
            if(inv.contractId === elem.id){
                invoicesRelated.push(inv);
            }
        })

        let contractTotalTtc = elem.totalTtc;

        return <>
            <div className="item item-main">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-3">
                            <div className="col-1">
                                <div className="name">
                                    <span>{elem.name}</span>
                                </div>
                            </div>
                            <div className="col-2">
                                <span className={"badge badge-" + elem.period}>{elem.periodString}</span>
                            </div>
                            <div className="col-3 actions" />
                        </div>
                    </div>
                </div>
            </div>
            {customers.length !== 0 ? customers.map((customer, index) => {

                let isUnderline = !customer.isActive;

                let refRelation = year+""+month;
                let invoice = null,
                    haveInvoiceGenerated = false;
                invoicesRelated.forEach(inv => {
                    if(inv.relationId === customer.relationId && inv.refRelation === refRelation){
                        invoice = inv;

                        if(inv.status !== STATUS_DRAFT){
                            haveInvoiceGenerated = true;
                        }
                    }
                })

                let [yearPlus, monthPlus] = helper.getDatePlusOne(customer.lastYear, customer.lastMonth);

                let dropdownItems = [
                    {data: <div onClick={() => this.handleRelationActive(customer)}>{customer.isActive ? "Désactiver" : "Activer"} ce client</div>},
                ];

                if(customer.isActive){
                    dropdownItems = [...[
                        {data: <div onClick={() => onGenerate(customer.relationId, invoice, year, month)}>Générer la facture</div>},
                    ], ...dropdownItems]
                }

                return <div className="item item-sub" key={index}>
                    <div className="item-content">
                        <div className="item-body">
                            <div className="infos infos-col-3">
                                <div className="col-1">
                                    <div className={"sub indent-24" + (isUnderline ? " linethrough" : "") }>
                                        {customer.name} {customer.site ? " - S.D.C " + customer.site.name : ""}
                                    </div>
                                </div>
                                <div className="col-2">
                                    <div className={"sub" + (invoice && contractTotalTtc !== invoice.totalTtc ? " txt-primary" : "")}>
                                        {Sanitaze.toFormatCurrency(invoice ? invoice.totalTtc : elem.totalTtc)}
                                    </div>
                                </div>
                                <div className="col-3 actions">
                                    {(!haveInvoiceGenerated && enableItemAction && year >= yearPlus && month >= monthPlus) && <>
                                        {customer.isActive && <>
                                            <ButtonIcon icon="vision" element="a" target="_blank"
                                                        onClick={Routing.generate(URL_PREVIEW_ELEMENT, {'year': year, 'month': month, 'id': customer.relationId})}
                                            >
                                                Prévisualiser
                                            </ButtonIcon>
                                            <ButtonIcon icon="pencil" element="a" target="_blank"
                                                        onClick={Routing.generate(URL_UPDATE_INVOICE, {'year': year, 'month': month, 'id': customer.relationId})}
                                            >
                                                Modifier
                                            </ButtonIcon>
                                        </>}
                                        <ButtonIconDropdown type="default" icon="more" items={dropdownItems} />
                                    </>}

                                    {haveInvoiceGenerated && invoice && <>
                                        <ButtonIcon icon="file" element="a" target="_blank" tooltipWidth={114}
                                                    onClick={Routing.generate(URL_DOWNLOAD_INVOICE, {'id': invoice.id})}
                                        >
                                            Télécharger la facture
                                        </ButtonIcon>
                                    </>}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            }) : <div className="item item-sub">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-2">
                            <div className="col-1">
                                <div className="sub indent-24 txt-danger">Aucun client lié à ce contrat.</div>
                            </div>
                            <div className="col-2 actions" />
                        </div>
                    </div>
                </div>
            </div>}
        </>
    }
}
