import "../../css/pages/paiements.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Orders } from "@dashboardPages/components/Paiement/Order/Orders";

Routing.setRoutingData(routes);

let el = document.getElementById("orders");
if(el){
    render(<Orders {...el.dataset} />, el)
}
