import "../../css/pages/ads.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";

import { Ads }          from "./components/Ads/Ads";
import { Alerts }       from "./components/Alerts/Alerts";
import { Estimations }  from "./components/Estimations/Estimations";
import { Devis }        from "@dashboardFolder/js/pages/components/Devis/Devis";
import { Demandes }     from "@dashboardFolder/js/pages/components/Demandes/Demandes";
import { Agency }       from "@dashboardFolder/js/pages/components/Agencies/Agency";

Routing.setRoutingData(routes);

let el = document.getElementById("ads");
if(el){
    render(<Ads {...el.dataset} />, el)
}

el = document.getElementById("agencies");
if(el){
    render(<Agency {...el.dataset} />, el)
}

el = document.getElementById("alerts");
if(el){
    render(<Alerts {...el.dataset} />, el)
}

el = document.getElementById("estimations");
if(el){
    render(<Estimations {...el.dataset} />, el)
}

el = document.getElementById("devis");
if(el){
    render(<Devis {...el.dataset} />, el)
}

el = document.getElementById("demandes");
if(el){
    render(<Demandes {...el.dataset} />, el)
}
