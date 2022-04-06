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
