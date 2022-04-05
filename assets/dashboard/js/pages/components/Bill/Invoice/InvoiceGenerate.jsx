import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Select }              from "@dashboardComponents/Tools/Fields";
import { Button }              from "@dashboardComponents/Tools/Button";
import { DatePick }            from "@dashboardComponents/Tools/DatePicker";
import { Alert }               from "@dashboardComponents/Tools/Alert";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_GENERATE_INVOICE     = "api_bill_invoices_generate";

export function InvoiceGenerateFormulaire ({ onUpdateList, element, dateInvoice })
{
    let form = <div />
    if(element){
        let url = Routing.generate(URL_GENERATE_INVOICE, {"id" : element.id});
        let msg = "Félicitations ! La génération de la facture s'est réalisée avec succès !";

        form = <Form
            url={url}

            dateInvoice={dateInvoice}

            dateAt={""}
            dueAt={""}
            dueType={Formulaire.setValueEmptyIfNull(element.dueType, 2)}

            onUpdateList={onUpdateList}
            messageSuccess={msg}
        />
    }

    return <div className="form">{form}</div>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            dateAt: props.dateAt,
            dueAt: props.dueAt,
            dueType: props.dueType,
            errors: [],
            success: false,
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => {
        const { dateAt } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "dueType"){
            helper.setDueAt(this, value, dateAt);
        }

        this.setState({[name]: value})
    }


    handleChangeDate = (name, e) => {
        const { dueType } = this.state;

        if(e !== null){
            e.setHours(0,0,0);

            if(name === "dueAt"){
                this.setState({ dueType: 0 })
            }

            if(name === "dateAt"){
                helper.setDueAt(this, dueType, e);
            }
        }

        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { dateAt, dueType } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateAt',      value: dateAt},
            {type: "text", id: 'dueType',     value: dueType}
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;
            axios({ method: method, url: url, data: this.state })
                .then(function (response) {
                    let data = response.data;
                    self.setState({ success: messageSuccess, errors: [] });
                    toastr.info(messageSuccess);
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
        const { dateInvoice } = this.props;
        const { errors, success, dateAt, dueAt, dueType } = this.state;

        let selectDueTypes = helper.getConditionPaiementChoices();

        let dateInvoiceString = dateInvoice.toLocaleDateString("fr");

        return <>
            <form onSubmit={this.handleSubmit}>

                <div className="line">
                    <div className="form-group">
                        <Alert type="info">
                            La date ne peut pas être inférieur à {dateInvoiceString} <br/><br/>
                            Afin de respecter la cohérence entre la date et le numéro
                            de facturation, <u>la date de facturation</u> doit être supérieure ou
                            égale à <b>{dateInvoiceString}</b>
                        </Alert>
                    </div>
                </div>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">
                    <DatePick identifiant="dateAt" valeur={dateAt} minDate={dateInvoice ? new Date(dateInvoice) : ""} errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)}>
                        Date de facture
                    </DatePick>
                    <div className="form-group" />
                </div>

                <div className="line line-2">
                    <Select items={selectDueTypes} identifiant="dueType" valeur={dueType} noEmpty={true} errors={errors} onChange={this.handleChange}>Conditions de paiement</Select>
                    {parseInt(dueType) !== 1 ? <>
                        <DatePick identifiant="dueAt" valeur={dueAt} minDate={dateAt ? dateAt : new Date()} errors={errors} onChange={(e) => this.handleChangeDate("dueAt", e)}>
                            Date d'échéance
                        </DatePick>
                    </> : <div className="form-group" />}
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>Valider</Button>
                    </div>
                </div>
            </form>
        </>
    }
}
