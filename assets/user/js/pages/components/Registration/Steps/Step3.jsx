import React from "react";

import { FormActions } from "@userPages/components/Registration/Registration";
import { Alert }       from "@dashboardComponents/Tools/Alert";

const CURRENT_STEP = 3;

export function Step3 ({ step, errors, onNext, email, session, workers, bank }) {
    let error = null;
    errors.length !== 0 && errors.forEach(err => {
        if(err.name === "bank"){
            error = <Alert type="danger">Veuillez sélectionner au moins 1 banque.</Alert>
        }
    })

    console.log(session)
    console.log(workers)
    console.log(bank)

    return <div className={"step-section step-workers" + (step === CURRENT_STEP ? " active" : "")}>

        <div className="review">
            <Alert type="reverse" title="Important !">
                Un mail de confirmation vous sera envoyé à l'adresse suivante : {email}
                <br/><br/>
                Suivez les instructions du mail pour <b>finaliser votre inscription</b>.
                <br/>
                Le mail de confirmation sera valide pendant <b>2 heures</b> suite à la validation de ce formulaire.
                <br/><br/>
                Veuillez vérifier vos courriers indésirables/spams.
            </Alert>

            <div className="review-infos">
                <h2>Inscription pour</h2>
                <div><b>{session.formation.name}</b> | {session.fullDate} pour une durée totale de {session.durationTotal}.</div>
                <div>Adresse : {session.fullAddress}</div>
            </div>

            {workers.map((el, index) => {
                return <div key={index}>{el.lastname}</div>
            })}
        </div>

        {error}

        <FormActions onNext={onNext} currentStep={CURRENT_STEP} />
    </div>
}