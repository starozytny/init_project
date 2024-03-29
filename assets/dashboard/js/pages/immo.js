import "../../css/pages/immo.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Agency } from "@dashboardPages/components/Immo/Agencies/Agency";
import { Negotiators }  from "@dashboardPages/components/Immo/Negociators/Negotiators";
import { Owners }       from "@dashboardPages/components/Immo/Owners/Owners";
import { Tenants }      from "@dashboardPages/components/Immo/Tenants/Tenants";
import { Prospects }    from "@dashboardPages/components/Immo/Prospects/Prospects";
import { Buyers }       from "@dashboardPages/components/Immo/Buyers/Buyers";

Routing.setRoutingData(routes);

let el = document.getElementById("agencies");
if(el){
    render(<Agency {...el.dataset} />, el)
}

el = document.getElementById("negotiators");
if(el){
    render(<Negotiators {...el.dataset} />, el)
}

el = document.getElementById("owners");
if(el){
    render(<Owners {...el.dataset} />, el)
}

el = document.getElementById("tenants");
if(el){
    render(<Tenants {...el.dataset} />, el)
}

el = document.getElementById("prospects");
if(el){
    render(<Prospects {...el.dataset} />, el)
}

el = document.getElementById("buyers");
if(el){
    render(<Buyers {...el.dataset} />, el)
}