import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input }               from "@dashboardComponents/Tools/Fields";
import { Button }              from "@dashboardComponents/Tools/Button";
import { DatePick }            from "@dashboardComponents/Tools/DatePicker";
import { Alert }               from "@dashboardComponents/Tools/Alert";

import Validateur              from "@commonComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_PAYEMENT_ELEMENT = "api_bill_invoices_payement";

export function InvoicePayementFormulaire ({ onUpdateList, onCloseAside, element })
{
    let form = <div />
    if(element){
        form = <Form
            url={Routing.generate(URL_PAYEMENT_ELEMENT, {'id': element.id})}

            element={element}
            price={element.toPay}

            onUpdateList={onUpdateList}
            onCloseAside={onCloseAside}
        />
    }

    return <div className="form">{form}</div>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            element: props.element,
            name: "",
            dateAt: new Date(),
            price: props.price,
            errors: [],
            success: false,
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeCleave = this.handleChangeCleave.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }
    handleChangeCleave = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.rawValue}) }
    handleChangeDate = (name, e) => {
        if(e !== null){ e.setHours(0,0,0); }
        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { url } = this.props;
        const { dateAt, price } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',    value: dateAt},
            {type: "text", id: 'price',     value: price}
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{

            Formulaire.loader(true);
            let self = this;
            axios({ method: "POST", url: url, data: this.state })
                .then(function (response) {
                    let data = response.data;
                    toastr.info("La paiement a été réalisée avec succès !")

                    if (self.props.onCloseAside) {
                        self.props.onCloseAside()
                    }
                    if (self.props.onUpdateList) {
                        self.props.onUpdateList(data, "update")
                    }
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(function () {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { errors, success, element, name, dateAt, price } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange} placeholder={"Entrer une description du paiement"}>
                        Description du paiement
                    </Input>
                </div>

                <div className="line line-2">
                    <DatePick identifiant="dateAt" valeur={dateAt} minDate={new Date(element.dateAtJavascript)} errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)}>
                        Date de paiement
                    </DatePick>
                    <Input type="cleave" valeur={price} identifiant="price" errors={errors} onChange={this.handleChangeCleave}>Montant</Input>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>Enregistrer</Button>
                    </div>
                </div>
            </form>
        </>
    }
}
