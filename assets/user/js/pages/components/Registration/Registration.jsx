import React, { Component } from "react";

import axios      from "axios";
import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Button } from "@dashboardComponents/Tools/Button";
import { Aside }  from "@dashboardComponents/Tools/Aside";

import Helper     from "@commonComponents/functions/helper";
import Validateur from "@commonComponents/functions/validateur";
import Formulaire from "@dashboardComponents/functions/Formulaire";
import helper     from "@userComponents/functions/helper";
import UpdateList from "@dashboardComponents/functions/updateList";

import { Step1 } from "@userPages/components/Registration/Steps/Step1";
import { Step2 } from "@userPages/components/Registration/Steps/Step2";

import { BankFormulaire } from "@userPages/components/Profil/Bank/BankForm";

const URL_CREATE_REGISTRATION = 'api_registration_create';

export class Registration extends Component {
    constructor(props) {
        super(props);

        this.state = {
            contextBank: "create",
            session: JSON.parse(props.session),
            allWorkers: JSON.parse(props.workers),
            allBanks: JSON.parse(props.banks),
            bank: null,
            workers: [],
            errors: [],
            step: 1
        }

        this.asideBank = React.createRef();

        this.handleNext = this.handleNext.bind(this);
        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSelectWorker = this.handleSelectWorker.bind(this);
        this.handleOpenAsideBank = this.handleOpenAsideBank.bind(this);

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleUpdateList = (type, element, context) => {
        console.log(type)
        switch (type){
            case "bank":
                let newData = UpdateList.update(context, this.state.allBanks, element);
                this.setState({ allBanks: newData, bank: element })
                break;
            default:
                break;
        }


    }

    handleSelectWorker = (worker) => {
        const { workers } = this.state;

        let nWorkers = helper.addOrRemove(workers, worker, "Membre sélectionné.", "Membre enlevé.");
        this.setState({ workers: nWorkers });
    }

    handleOpenAsideBank = (contextBank, bank= null) => {
        this.setState({ contextBank, bank })
        this.asideBank.current.handleOpen();
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
        const { step, contextBank, bank } = this.state;

        let steps = [
            {id: 1, label: "Participants"},
            {id: 2, label: "Compte(s) bancaire(s)"},
            {id: 3, label: "Récapitulatif"},
            {id: 4, label: "Validation"},
        ];

        let stepTitle = "Etape 1 : Participants";
        let stepsItems = [];
        steps.forEach(el => {
            let active = "";
            if(el.id === step){
                active = " active";
                stepTitle = "Etape " + el.id + " : " + el.label;
            }

            stepsItems.push(<div className={"item" + active} key={el.id} onClick={() => this.handleNext(el.id, step, true)}>
                <span className="number">{el.id} - </span>
                <span className="label">{el.label}</span>
            </div>)
        })

        let contentBank = contextBank === "create" ? <BankFormulaire type="create" isRegistration={true} onUpdateList={this.handleUpdateList}/>
            : <BankFormulaire type="update" element={bank} isRegistration={true} onUpdateList={this.handleUpdateList}/>

        return <div className="main-content">
            <div className="steps">
                {stepsItems}
            </div>

            <h2>{stepTitle}</h2>

            <form onSubmit={this.handleSubmit}>

                <Step1 {...this.state} onNext={this.handleNext} onSelectWorker={this.handleSelectWorker} />

                <Step2 {...this.state} onNext={this.handleNext} onSelectWorker={this.handleSelectWorker}
                       onOpenAside={this.handleOpenAsideBank} />

            </form>

            <Aside ref={this.asideBank} content={contentBank} />
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