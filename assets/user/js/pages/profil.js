import "../../css/pages/profil.scss";

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from "react";
import { render } from "react-dom";
import { Users }          from "@userPages/components/Profil/User/Users";
import { UserFormulaire } from "@userPages/components/Profil/UserForm";

Routing.setRoutingData(routes);

let el = document.getElementById("profil-update");
if(el){
    render(<div className="main-content">
        <UserFormulaire type="profil"
                        element={JSON.parse(el.dataset.donnees)}
                        societyId={el.dataset.societyId} />
    </div>, el)
}

el = document.getElementById("profil-users");
if(el){
    render(<Users {...el.dataset} />, el)
}

el = document.getElementById("user-create");
if(el){
    render(<div className="main-content">
        <UserFormulaire type="create" societyId={el.dataset.societyId} />
    </div>, el)
}

el = document.getElementById("user-update");
if(el){
    render(<div className="main-content">
        <UserFormulaire type="update" element={JSON.parse(el.dataset.donnees)} societyId={el.dataset.societyId} />
    </div>, el)
}