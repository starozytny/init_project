import "../../css/pages/profil.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { UserFormulaire } from "@userComponents/Profil/UserForm";
import { BankFormulaire } from "@userComponents/Profil/BankForm";

Routing.setRoutingData(routes);

let el = document.getElementById("profil-update");
if(el){
    render(<div className="main-content">
        <UserFormulaire type="profil" element={JSON.parse(el.dataset.donnees)} />
    </div>, el)
}

el = document.getElementById("bank-create");
if(el){
    render(<div className="main-content">
        <BankFormulaire type="create" />
    </div>, el)
}
