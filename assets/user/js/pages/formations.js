import "../../css/pages/formations.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Sessions } from "@userComponents/Session/Sessions";

Routing.setRoutingData(routes);

let el = document.getElementById("sessions");
if(el){
    render(<div className="main-content">
        <Sessions {...el.dataset} />
    </div>, el)
}