import "../../css/pages/society.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Societies } from "@dashboardPages/components/Society/Societies";

Routing.setRoutingData(routes);

let el = document.getElementById("societies");
if(el){
    render(<Societies {...el.dataset}/>, el)
}
