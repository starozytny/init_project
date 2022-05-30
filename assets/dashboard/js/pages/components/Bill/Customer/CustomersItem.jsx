import React, { Component } from 'react';

import Routing        from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import Sanitaze       from "@commonComponents/functions/sanitaze";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { Selector }   from "@dashboardComponents/Layout/Selector";

const URL_DOWNLOAD_INVOICE = "api_bill_invoices_download"

export class CustomersItem extends Component {
    render () {
        const { filters, invoices, elem, onChangeContext, onDelete } = this.props;

        let invoicesRelated = [];
        invoices.forEach(inv => {
            if(inv.customerId === elem.id){
                invoicesRelated.push(inv)
            }
        })

        let displayInvoice = filters && filters[0] === 0;

        return <>
            <div className={"item" + (displayInvoice ? " item-main" : "")}>
                <Selector id={elem.id} />

                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-3">
                            <div className="col-1">
                                <div className="name">
                                    <span>{elem.name}</span>
                                </div>
                                <div className="sub">{elem.numero}</div>
                            </div>
                            <div className="col-2">
                                <div className="sub">{elem.email}</div>
                                <div className="sub">{elem.phone}</div>
                            </div>
                            <div className="col-3 actions">
                                <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                                <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {displayInvoice && invoicesRelated.map(invoice => {
                return <div className="item item-sub" key={invoice.id}>
                    <div className="selector" />
                    <div className="item-content">
                        <div className="item-body">
                            <div className="infos infos-col-3">
                                <div className="col-1">
                                    <div className="name">
                                        <span><span className="sub">{invoice.dateAtString}</span> - {invoice.numero === "Z-Brouillon" ? "Brouillon" : invoice.numero}</span>
                                    </div>
                                </div>
                                <div className="col-2">
                                    <div className="sub">{Sanitaze.toFormatCurrency(invoice.totalTtc)} TTC</div>
                                </div>
                                <div className="col-3 actions">
                                    <ButtonIcon icon="receipt" element="a" target="_blank" onClick={Routing.generate(URL_DOWNLOAD_INVOICE, {'id': invoice.id})}>Télécharger</ButtonIcon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            })}
        </>
    }
}

export class CustomersContractItem extends Component {
    render () {
        const { elem, onChangeRelation, contract, sites, relations } = this.props;

        let active = false;
        if(relations && contract){
            relations.forEach(re => {
                if(re.contract.id === contract.id && re.customer.id === elem.id && re.site == null){
                    active = true;
                }
            })
        }

        return <>
            <div className="item item-main">
                <div className="selector" onClick={() => onChangeRelation("customer", contract, elem, active ? "delete" : "create")}>
                    <label className={"item-selector " + (active)}/>
                </div>

                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-3">
                            <div className="col-1">
                                <div className="name">
                                    <span>{elem.name}</span>
                                </div>
                                <div className="sub">{elem.numero}</div>
                            </div>
                            <div className="col-2">
                                <div className="sub">{elem.email}</div>
                                <div className="sub">{elem.phone}</div>
                            </div>
                            <div className="col-3 actions" />
                        </div>
                    </div>
                </div>
            </div>
            {sites.length !== 0 && sites.map(site => {
                if(site.customer && site.customer.id === elem.id){
                    let active = false;
                    if(relations && contract){
                        relations.forEach(re => {
                            if(re.contract.id === contract.id && re.customer.id === elem.id && (re.site && re.site.id === site.id)){
                                active = true;
                            }
                        })
                    }

                    return <div className="item item-sub" key={site.id}>
                        <div className="selector" onClick={() => onChangeRelation("site", contract, site, active ? "delete" : "create")}>
                            <label className={"item-selector " + (active)}/>
                        </div>
                        <div className="item-content">
                            <div className="item-body">
                                <div className="infos infos-col-3">
                                    <div className="col-1">
                                        <div className="sub">{site.numero}</div>
                                        <div className="name">
                                            <span>{site.name}</span>
                                        </div>
                                    </div>
                                    <div className="col-2">
                                        <div className="sub">{site.address}</div>
                                        <div className="sub">{site.address2}</div>
                                        <div className="sub">{site.complement}</div>
                                        <div className="sub">{site.zipcode}, {site.city}</div>
                                    </div>
                                    <div className="col-3 actions" />
                                </div>
                            </div>
                        </div>
                    </div>
                }
            })}
        </>
    }
}
