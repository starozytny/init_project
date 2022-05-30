import "../../css/pages/bill.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Invoices } from "@dashboardPages/components/Bill/Invoice/Invoices";
import { Items }    from "@dashboardPages/components/Bill/Item/Items";
import { Taxes } from "@dashboardPages/components/Bill/Taxe/Taxes";
import { Unities } from "@dashboardPages/components/Bill/Unity/Unities";
import { Customers } from "@dashboardPages/components/Bill/Customer/Customers";
import { Sites } from "@dashboardPages/components/Bill/Site/Sites";
import { Contracts } from "@dashboardPages/components/Bill/Contract/Contracts";
import { ContractsProcess } from "@dashboardPages/components/Bill/Contract/ContractsProcess";
import { Quotations } from "@dashboardPages/components/Bill/Quotation/Quotations";
import { DocumentFormulaire } from "@dashboardPages/components/Bill/components/DocumentFormulaire";
import { SocietyFormulaire } from "@dashboardPages/components/Bill/Society/SocietyForm";
import { Avoirs } from "@dashboardPages/components/Bill/Avoir/Avoirs";

Routing.setRoutingData(routes);

let el = document.getElementById("invoices");
if(el){
    render(<Invoices {...el.dataset}/>, el)
}

el = document.getElementById("items");
if(el){
    render(<Items {...el.dataset}/>, el)
}

el = document.getElementById("taxes");
if(el){
    render(<Taxes {...el.dataset}/>, el)
}

el = document.getElementById("unities");
if(el){
    render(<Unities {...el.dataset}/>, el)
}

el = document.getElementById("customers");
if(el){
    render(<Customers {...el.dataset}/>, el)
}

el = document.getElementById("society");
if(el){
    render(<SocietyFormulaire type="update" element={JSON.parse(el.dataset.element)} />, el)
}

el = document.getElementById("notes");
if(el){
    render(<SocietyFormulaire type="update" typeForm="notes" element={JSON.parse(el.dataset.element)} />, el)
}

el = document.getElementById("sites");
if(el){
    render(<Sites {...el.dataset} classes=" "/>, el)
}

el = document.getElementById("contracts");
if(el){
    render(<Contracts {...el.dataset} classes=" "/>, el)
}

el = document.getElementById("contracts-process");
if(el){
    render(<ContractsProcess {...el.dataset} classes=" "/>, el)
}

el = document.getElementById("quotations");
if(el){
    render(<Quotations {...el.dataset} classes=" "/>, el)
}

el = document.getElementById("avoirs");
if(el){
    render(<Avoirs {...el.dataset} classes=" "/>, el)
}


el = document.getElementById("invoice-create");
if(el){
    let data = getData(el);
    let quotationId = el.dataset.quotationId ? parseInt(el.dataset.quotationId) : null;
    let quotationRef = el.dataset.quotationRef ? el.dataset.quotationRef : null;

    render(<DocumentFormulaire page="invoice" type="create" quotationId={quotationId} quotationRef={quotationRef} {...data} />, el)
}

el = document.getElementById("invoice-update");
if(el){
    let data = getData(el);
    let quotationId = el.dataset.quotationId ? parseInt(el.dataset.quotationId) : null;
    let quotationRef = el.dataset.quotationRef ? el.dataset.quotationRef : null;

    render(<DocumentFormulaire page="invoice" type="update" quotationId={quotationId} quotationRef={quotationRef} {...data} />, el)
}

el = document.getElementById("avoir-create");
if(el){
    let data = getData(el);
    let invoiceId = el.dataset.invoiceId ? parseInt(el.dataset.invoiceId) : null;
    let invoiceRef = el.dataset.invoiceRef ? el.dataset.invoiceRef : null;

    render(<DocumentFormulaire page="avoir" type="create" invoiceId={invoiceId} invoiceRef={invoiceRef} {...data} />, el)
}

el = document.getElementById("quotation-create");
if(el){
    let data = getData(el);

    render(<DocumentFormulaire page="quotation" type="create" {...data} />, el)
}

function getData (el) {
    let society = JSON.parse(el.dataset.society);
    let taxes = JSON.parse(el.dataset.taxes);
    let unities = JSON.parse(el.dataset.unities);
    let items = JSON.parse(el.dataset.items);
    let products = JSON.parse(el.dataset.products);
    let customers = JSON.parse(el.dataset.customers);
    let sites = JSON.parse(el.dataset.sites);
    let element = el.dataset.donnees ? JSON.parse(el.dataset.donnees) : null;

    return {
        society: society,
        taxes: taxes,
        unities: unities,
        items: items,
        products: products,
        customers: customers,
        sites: sites,
        element: element
    };
}