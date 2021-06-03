import "../../css/pages/ads.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";

import { Ads }          from "./components/Ads/Ads";
import { Alerts }       from "./components/Alerts/Alerts";
import { Estimations }  from "./components/Estimations/Estimations";
import { Devis }        from "@dashboardFolder/js/pages/components/Devis/Devis";

Routing.setRoutingData(routes);

let el = document.getElementById("ads");
if(el){
    render(<Ads />, el)
}

el = document.getElementById("alerts");
if(el){
    render(<Alerts />, el)
}

el = document.getElementById("estimations");
if(el){
    render(<Estimations />, el)
}

el = document.getElementById("devis");
if(el){
    render(<Devis />, el)
}
