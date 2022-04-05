import React, { Component } from 'react';

import { Input }               from "@dashboardComponents/Tools/Fields";
import { Button }              from "@dashboardComponents/Tools/Button";
import { DatePick }            from "@dashboardComponents/Tools/DatePicker";
import { Alert }               from "@dashboardComponents/Tools/Alert";

import Validateur              from "@commonComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function InvoicePayementFormulaire ({ onUpdateList, onCloseAside, element })
{
    let form = <div />
    if(element){
        form = <Form

            element={element}
            total={element.totalTtc}

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
            content: "",
            dateAt: new Date(),
            total: props.total,
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

        const { dateAt, total } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',    value: dateAt},
            {type: "text", id: 'total',     value: total}
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{

        }
    }

    render () {
        const { errors, success, element, content, dateAt, total } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">
                    <Input valeur={content} identifiant="content" errors={errors} onChange={this.handleChangeCleave} placeholder={"Entrer une desription du paiement"}>
                        Description du paiement
                    </Input>
                </div>

                <div className="line line-2">
                    <DatePick identifiant="dateAt" valeur={dateAt} minDate={new Date(element.dateAtJavascript)} errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)}>
                        Date de paiement
                    </DatePick>
                    <Input type="cleave" valeur={total} identifiant="total" errors={errors} onChange={this.handleChangeCleave}>Montant</Input>
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
