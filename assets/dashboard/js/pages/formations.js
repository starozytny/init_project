import "../../css/pages/formations.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Formations } from "./components/Formations/Formations";
import { Sessions }   from "./components/Formations/Sessions/Sessions";

Routing.setRoutingData(routes);

let el = document.getElementById("formations");
if(el){
    render(<Formations {...el.dataset}/>, el)
}

el = document.getElementById("formation-sessions");
if(el){
    render(<Sessions {...el.dataset}/>, el)
}
