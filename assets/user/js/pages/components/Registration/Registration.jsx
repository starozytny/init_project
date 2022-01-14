import React, { Component } from "react";

import axios      from "axios";
import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button } from "@dashboardComponents/Tools/Button";
import { Alert }  from "@dashboardComponents/Tools/Alert";

import Validateur from "@commonComponents/functions/validateur";
import Formulaire from "@dashboardComponents/functions/Formulaire";
import helper     from "@userComponents/functions/helper";

import { TeamList } from "@userPages/components/Profil/Team/TeamList";

const URL_CREATE_REGISTRATION = 'api_registration_create';

export class Registration extends Component {
    constructor(props) {
        super(props);

        this.state = {
            session: JSON.parse(props.session),
            allWorkers: JSON.parse(props.workers),
            workers: []
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleSelectWorker = this.handleSelectWorker.bind(this);
    }

    handleSelectWorker = (worker) => {
        const { workers } = this.state;

        let nWorkers = helper.addOrRemove(workers, worker, "Membre sélectionné.", "Membre enlevé.");

        this.setState({ workers: nWorkers });
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { session, workers } = this.state;

        let paramsToValidate = [];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            // TO ADJUST
            let workersId = [];
            workers.forEach(worker => {
                workersId.push(worker.id)
            })

            this.state.workersId = workersId;

            axios({ method: "POST", url: Routing.generate(URL_CREATE_REGISTRATION, {'session': session.id}), data: this.state })
                .then(function (response) {
                    let data = response.data;
                    document.body.scrollTop = 0;
                    document.documentElement.scrollTop = 0;
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(() => {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { allWorkers, workers } = this.state;

        return <div className="main-content">
            <form onSubmit={this.handleSubmit}>

                <div className="step step-1">
                    <TeamList isRegistration={true} onSelectWorker={this.handleSelectWorker}
                              data={allWorkers} workers={workers} />
                </div>

                <div className="line line-buttons">
                    <div className="form-button">
                        <Button isSubmit={true}>Valider</Button>
                    </div>
                </div>
            </form>
        </div>
    }
}