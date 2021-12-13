import "../../css/pages/formations.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Formations } from "@userComponents/Formation/Formations";

Routing.setRoutingData(routes);

let el = document.getElementById("formations");
if(el){
    render(<div className="main-content">
        <Formations {...el.dataset} />
    </div>, el)
}