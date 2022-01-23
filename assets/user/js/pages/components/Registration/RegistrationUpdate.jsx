import React, { Component } from "react";

import axios      from "axios";
import Routing    from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Aside }  from "@dashboardComponents/Tools/Aside";
import { Alert }  from "@dashboardComponents/Tools/Alert";

import Validateur from "@commonComponents/functions/validateur";
import Formulaire from "@dashboardComponents/functions/Formulaire";
import UpdateList from "@dashboardComponents/functions/updateList";

import { BankFormulaire } from "@userPages/components/Profil/Bank/BankForm";
import { TeamItemRegistrationUpdate } from "@userPages/components/Profil/Team/TeamItem";

const URL_UPDATE_REGISTRATION = 'api_registration_create';
const URL_DELETE_BANK         = 'api_banks_delete';

export class RegistrationUpdate extends Component {
    constructor(props) {
        super(props);

        this.state = {
            contextBank: "create",
            session: JSON.parse(props.session),
            allWorkers: JSON.parse(props.workers),
            allBanks: JSON.parse(props.banks),
            bank: null,
            workers: JSON.parse(props.workersRegistered),
            errors: [],
        }

        this.asideBank = React.createRef();

        this.handleUpdateList = this.handleUpdateList.bind(this);
        this.handleSelectBank = this.handleSelectBank.bind(this);
        this.handleDeleteBank = this.handleDeleteBank.bind(this)
        this.handleOpenAsideBank = this.handleOpenAsideBank.bind(this);

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleUpdateList = (element, context, type) => {
        switch (type){
            default:
                let newData = UpdateList.update(context, this.state.allBanks, element);
                this.setState({ allBanks: newData, bank: element })
                if(this.asideBank.current) this.asideBank.current.handleClose();
                break;
        }
    }

    handleDeleteBank = (element, msg, text='Cette action est irréversible.') => {
        let url = Routing.generate(URL_DELETE_BANK, {'id': element.id})
        Formulaire.axiosDeleteElement(this, element, url, "Supprimer ce RIB ?", text);
    }

    handleSelectBank = (bank) => { this.setState({ bank }) }

    handleOpenAsideBank = (contextBank, bank= null) => {
        this.setState({ contextBank, bank })
        this.asideBank.current.handleOpen();
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

            // axios({ method: "POST", url: Routing.generate(URL_UPDATE_REGISTRATION, {'session': session.id}), data: this.state })
            //     .then(function (response) {
            //         let data = response.data;
            //         Helper.toTop();
            //         self.setState({ step: 4 })
            //     })
            //     .catch(function (error) {
            //         Formulaire.displayErrors(self, error);
            //     })
            //     .then(() => {
            //         Formulaire.loader(false);
            //     })
            // ;
        }
    }

    render () {
        const { contextBank, bank, workers } = this.state;

        let contentBank = contextBank === "create" ? <BankFormulaire type="create" isRegistration={true} onUpdateList={this.handleUpdateList}/>
            : <BankFormulaire type="update" element={bank} isRegistration={true} onUpdateList={this.handleUpdateList} key={bank.id}/>

        return <div className="main-content">

            <form onSubmit={this.handleSubmit}>
                <div>
                    <div className="items-table">
                        <div className="items items-default">
                            <div className="item item-header">
                                <div className="item-content">
                                    <div className="item-body">
                                        <div className="infos infos-col-2">
                                            <div className="col-1">Equipe</div>
                                            <div className="col-2 actions" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {workers && workers.length !== 0 ? workers.map(elem => {
                                return <TeamItemRegistrationUpdate {...this.state} elem={elem} key={elem.id}/>
                            }) : <Alert>Aucun résultat</Alert>}
                        </div>
                    </div>
                </div>
            </form>

            <Aside ref={this.asideBank} content={contentBank} />
        </div>
    }
}