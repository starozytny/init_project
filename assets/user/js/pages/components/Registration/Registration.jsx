import React, { Component } from "react";

import axios      from "axios";
import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button } from "@dashboardComponents/Tools/Button";

import Helper     from "@commonComponents/functions/helper";
import Validateur from "@commonComponents/functions/validateur";
import Formulaire from "@dashboardComponents/functions/Formulaire";
import helper     from "@userComponents/functions/helper";

import { TeamList } from "@userPages/components/Profil/Team/TeamList";
import {Step1} from "@userPages/components/Registration/Steps/Step1";

const URL_CREATE_REGISTRATION = 'api_registration_create';

export class Registration extends Component {
    constructor(props) {
        super(props);

        this.state = {
            session: JSON.parse(props.session),
            allWorkers: JSON.parse(props.workers),
            workers: [],
            errors: [],
            step: 1
        }


        this.handleNext = this.handleNext.bind(this);
        this.handleSelectWorker = this.handleSelectWorker.bind(this);

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleSelectWorker = (worker) => {
        const { workers } = this.state;

        let nWorkers = helper.addOrRemove(workers, worker, "Membre sélectionné.", "Membre enlevé.");
        this.setState({ workers: nWorkers });
    }

    handleNext = (stepClicked, stepInitial = null) => {
        const { workers } = this.state;

        let paramsToValidate = [];
        if(stepInitial === null){
            switch (stepClicked){
                default:
                    paramsToValidate = [
                        {type: "array",  id: 'workers', value: workers},
                    ];
                    break;
            }
        }

        // validate global
        let validate = Validateur.validateur(paramsToValidate);

        Helper.toTop();
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            this.setState({ errors: [], step: stepClicked })
        }
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
                    Helper.toTop();
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
        const { step } = this.state;

        let steps = [
            {id: 1, label: "Participants"},
            {id: 2, label: "Compte(s) bancaire(s)"},
            {id: 3, label: "Récapitulatif"},
            {id: 4, label: "Validation"},
        ];

        let stepTitle = "Etape 1 : Participants";
        let stepsItems = [];
        {steps.forEach(el => {
            let active = "";
            if(el.id === step){
                active = " active";
                stepTitle = "Etape " + el.id + " : " + el.label;
            }

            stepsItems.push(<div className={"item" + active} key={el.id} onClick={() => this.handleNext(el.id, step, true)}>
                <span className="number">{el.id} - </span>
                <span className="label">{el.label}</span>
            </div>)
        })}

        return <div className="main-content">
            <div className="steps">
                {stepsItems}
            </div>

            <h2>{stepTitle}</h2>

            <form onSubmit={this.handleSubmit}>

                <Step1 {...this.state} onSelectWorker={this.handleSelectWorker} />


            </form>
        </div>
    }
}


export function FormActions ({ onNext, currentStep }) {
    return <div className="line line-buttons">
        <Button type="reverse" onClick={() => onNext(currentStep - 1, currentStep)}>Etape précédente</Button>
        <div/>
        <div className="btns-submit">
            <Button onClick={() => onNext(currentStep + 1)}>Etape suivante</Button>
        </div>
    </div>
}