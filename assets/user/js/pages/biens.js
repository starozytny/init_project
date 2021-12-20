import "../../css/pages/biens.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";

import { Biens }          from "./components/Biens/Biens";
import { BienFormulaire } from "./components/Biens/BienForm";

Routing.setRoutingData(routes);

let el = document.getElementById("list-biens");
if(el){
    render(<Biens {...el.dataset} />, el)
}

el = document.getElementById("create-bien");
if(el){
    render(<BienFormulaire type="create"
                           negociators={JSON.parse(el.dataset.negotiators)} />, el)
}

el = document.getElementById("update-bien");
if(el){
    render(<BienFormulaire type="update" element={JSON.parse(el.dataset.element)}
                           negociators={JSON.parse(el.dataset.negotiators)} />, el)
}