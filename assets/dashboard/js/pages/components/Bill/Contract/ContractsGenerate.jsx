import React, { Component } from "react";

import { Button, ButtonIcon } from "@dashboardComponents/Tools/Button";
import { DatePick }     from "@dashboardComponents/Tools/DatePicker";
import { Select }       from "@dashboardComponents/Tools/Fields";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import helper       from "@dashboardPages/components/Bill/functions/helper";
import Formulaire   from "@dashboardComponents/functions/Formulaire";
import Validateur   from "@commonComponents/functions/validateur";

export class ContractsGenerate extends Component {
    render () {
        const { data, dateInvoice, month, onUpdateData, onUpdateDateElement, onGenerate } = this.props;

        let dateInvoiceString = dateInvoice.toLocaleDateString("fr");

        return <>
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

            <div className="line">
                <div className="form-group">
                    <Button onClick={onGenerate}>Lancer la facturation</Button>
                </div>
            </div>

            <div className="items-table">
                <div className="items items-default">
                    <div className="item item-header">
                        <div className="item-content">
                            <div className="item-body">
                                <div className="infos infos-col-3">
                                    <div className="col-1">Contrat</div>
                                    <div className="col-2" />
                                    <div className="col-3 actions" />
                                </div>
                            </div>
                        </div>
                    </div>
                    {data && data.length !== 0 ? data.map((item, index) => {

                        let elem = item.contract;

                        return <React.Fragment key={index}>
                            <div className="item item-main">
                                <div className="item-content">
                                    <div className="item-body">
                                        <div className="infos infos-col-2">
                                            <div className="col-1">
                                                <div className="name">
                                                    <span><span className="sub">#{elem.numero} - </span><span>{elem.name}</span></span>
                                                </div>
                                            </div>
                                            <div className="col-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {item.elements.map((el, index) => {
                                return <DatesFormulaire month={month} element={el} dateInvoice={dateInvoice} key={index}
                                                        onUpdateData={onUpdateData} onUpdateDateElement={onUpdateDateElement} />
                            })}
                        </React.Fragment>

                    }) : <Alert>Aucun résultat</Alert>}
                </div>
            </div>
        </>
    }
}

function DatesFormulaire ({ month, element, dateInvoice, onUpdateData, onUpdateDateElement })
{
    return <Form
        dateInvoice={dateInvoice}

        month={month}
        element={element}

        dateAt={Formulaire.setDateOrEmptyIfNull(element.dateAt)}
        dueAt={Formulaire.setDateOrEmptyIfNull(element.dueAt)}
        dueType={Formulaire.setValueEmptyIfNull(element.dueType, 0)}

        onUpdateData={onUpdateData}
        onUpdateDateElement={onUpdateDateElement}
    />
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            dateAt: props.dateAt,
            dueAt: props.dueAt,
            dueType: props.dueType,
            errors: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => {
        const { dateAt } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        name = name.substring(0, name.indexOf('-'))

        if(name === "dueType"){
            helper.setDueAt(this, value, dateAt);
        }

        this.setState({[name]: value})
    }


    handleChangeDate = (name, e) => {
        const { dueAt, dueType } = this.state;

        let value = e !== null ? e : "";

        helper.changeDateDocument(this, value, name, this.state.dueType);

        if(parseInt(dueType) === 0){
            this.setState({ dueAt: value })
        }

        this.setState({ [name]: value })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { dateInvoice, element } = this.props;
        const { dateAt, dueAt, dueType } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: "dateAt-" + element.id,  value: dateAt},
            {type: "text", id: 'dueType-'  + element.id, value: dueType}
        ];

        paramsToValidate = helper.validateDates("invoice", paramsToValidate, dateInvoice, dateAt, dueAt, dueType, "", "dateAt-" + element.id, "dueAt-" + element.id);

        // validate global
        let validate = Validateur.validateur(paramsToValidate);
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            this.props.onUpdateData(element, this.state);
        }
    }

    render () {
        const { dateInvoice, element, onUpdateDateElement, month } = this.props;
        const { errors, dateAt, dueAt, dueType } = this.state;

        let selectDueTypes = helper.getConditionPaiementChoices();

        let maxDate = new Date();
        maxDate.setDate(31)
        maxDate.setMonth(month - 1)

        return <>
            <div className="item item-sub">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-3">
                            <div className="col-1">
                                <div className="sub indent-24">{element.customer.name}</div>
                            </div>
                            {!element.isValid ? <>
                                <div className="col-2">
                                    <div className="line line-3">
                                        <DatePick identifiant={"dateAt-" + element.id} valeur={dateAt} minDate={dateInvoice ? new Date(dateInvoice) : ""} maxDate={maxDate}
                                                  placeholder="Date de facture"
                                                  errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)} />

                                        <Select items={selectDueTypes} identifiant={"dueType-" + element.id} valeur={dueType} noEmpty={true}
                                                placeholder="Conditions de paiement"
                                                errors={errors} onChange={this.handleChange} />

                                        {parseInt(dueType) !== 1 ? <>
                                            <DatePick identifiant={"dueAt-" + element.id} valeur={dueAt} minDate={dateAt ? dateAt : new Date()} maxDate={maxDate}
                                                      placeholder="Date d'échéance"
                                                      errors={errors} onChange={(e) => this.handleChangeDate("dueAt", e)} />
                                        </> : <div className="form-group" />}
                                    </div>
                                </div>
                                <div className="col-3 actions">
                                    <Button onClick={this.handleSubmit} type="default" outline={true}>Valider</Button>
                                </div>
                            </> : <>
                                <div className="col-2">
                                    <div className="sub">Date de facture le {new Date(element.dateAt).toLocaleDateString("fr")}</div>
                                </div>
                                <div className="col-3 actions">
                                    <ButtonIcon icon="pencil" onClick={() => onUpdateDateElement(element)}>Modifier</ButtonIcon>
                                </div>
                            </>}
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}

