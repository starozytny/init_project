import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Radiobox, Select, TextArea } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button, ButtonIcon }  from "@dashboardComponents/Tools/Button";
import { DatePick }            from "@dashboardComponents/Tools/DatePicker";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

import { Products }            from "@dashboardPages/components/Bill/components/Products";
import { Totaux, Tvas }        from "@dashboardPages/components/Bill/components/DocumentFormulaire";

const URL_CREATE_ELEMENT     = "api_bill_contracts_create";
const URL_UPDATE_GROUP       = "api_bill_contracts_update";
const API_NUMERO_RELATION    = "api_bill_contracts_relation_update_numero";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let i = 0;

export function ContractFormulaire ({ type, onChangeContext, onUpdateList, element, societyId, counterContract, yearContract,
                                        items, products, taxes, unities })
{
    let prefix = "CO";
    let title = "Ajouter un contrat";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouveau contrat !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let today = new Date();
    let numero = helper.guessNumero(prefix, parseInt(yearContract), today.getFullYear(), parseInt(counterContract));

    let [nProducts, totalHt, totalRemise, totalTva, totalTtc] = helper.getProductsAndTotal(element, products);

    let dateAt = element ? Formulaire.setDateOrEmptyIfNull(element.dateAtJavascript) : new Date();
    // let dueAt = new Date();
    // dueAt = element ? Formulaire.setDateOrEmptyIfNull(element.dueAtJavascript) : new Date(dueAt);

    let form = <Form
        context={type}
        url={url}

        societyId={societyId}
        counterContract={counterContract}
        yearContract={yearContract}

        items={items}
        taxes={taxes}
        unities={unities}

        numero={element ? Formulaire.setValueEmptyIfNull(element.numero, numero) : numero}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        theme={element ? Formulaire.setValueEmptyIfNull(element.theme, 1) : 1}
        period={element ? Formulaire.setValueEmptyIfNull(element.period, 1) : 1}
        dateAt={dateAt}
        dueAt={dateAt}
        dueType={element ? Formulaire.setValueEmptyIfNull(element.dueType, 0) : 0}
        duration={element ? Formulaire.setValueEmptyIfNull(element.duration) : ""}

        products={nProducts}

        totalHt={totalHt}
        totalRemise={totalRemise}
        totalTva={totalTva}
        totalTtc={totalTtc}

        note={element ? Formulaire.setValueEmptyIfNull(element.note) : ""}
        footer={element ? Formulaire.setValueEmptyIfNull(element.footer) : ""}

        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            numero: props.numero,
            name: props.name,
            theme: props.theme,
            period: props.period,
            dateAt: props.dateAt,
            dueAt: props.dueAt,
            dueType: props.dueType,
            duration: props.duration,

            products: props.products,
            totalHt: props.totalHt,
            totalRemise: props.totalRemise,
            totalTva: props.totalTva,
            totalTtc: props.totalTtc,

            note: props.note,
            footer: props.footer,

            element: null,
            item: "",

            errors: [],
            success: false
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
        helper.changeDateDocument(this, e, name, this.state.dueType);

        this.setState({ [name]: e !== null ? e : "" })
    }

    handleSelectItem = (item) => { helper.selectProduct(this, this.props.items, item); }

    handleChangeItems = (item, context) => { helper.changeProducts(this, context, this.state.products, this.state.totalRemise, item); }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name, theme, period, dateAt, dueType, dueAt } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text",  id: 'name',   value: name},
            {type: "text",  id: 'theme',  value: theme},
            {type: "text",  id: 'period',  value: period},
            {type: "date",  id: 'dateAt',  value: dateAt},
            {type: "text",  id: 'dueType',  value: dueType},
        ];

        if(parseInt(dueType) !== 1){
            if(dueAt === ""){
                paramsToValidate = [...paramsToValidate,
                    ...[{type: "date", id: 'dueAt', value: dueAt}]
                ];
            }else{
                paramsToValidate = [...paramsToValidate,
                    ...[{type: "dateCompare", id: 'dueAt', value: dateAt, idCheck: 'dateAt', valueCheck: dueAt}]
                ];
            }
        }

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

                    setTimeout(() => {
                        location.reload()
                    }, 2000)
                })
                .catch(function (error) {
                    Formulaire.loader(false);
                    Formulaire.displayErrors(self, error);
                })
            ;
        }
    }

    render () {
        const { context, societyId, items, taxes, unities } = this.props;
        const { item, element, errors, success, products, numero, name, period,
            dateAt, dueType, dueAt, duration,
            totalHt, totalRemise, totalTva, totalTtc,
            note, footer } = this.state;

        let itemsPeriod = [
            {value: 1, label: "Mensuelle",      identifiant: 'pe-1'},
            {value: 2, label: "Trimestrielle",  identifiant: 'pe-2'},
            {value: 3, label: "Semestrielle",   identifiant: 'pe-3'},
            {value: 4, label: "Annuelle",       identifiant: 'pe-4'},
        ]

        let selectDueTypes = helper.getConditionPaiementChoices();
        let tvas = helper.getTvasTab(products);

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Informations générales</div>
                        </div>

                        <div className="line line-2">
                            <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>* Désignation</Input>
                            {/*<div className="form-group">*/}
                            {/*    <label>Numéro d'identification</label>*/}
                            {/*    <div>{numero}</div>*/}
                            {/*</div>*/}
                            <div className="form-group" />
                        </div>
                    </div>

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Fréquence</div>
                        </div>

                        {/*<div className="line line-3">*/}
                        {/*    <DatePick identifiant="dateAt" valeur={dateAt} minDate={new Date()}*/}
                        {/*              errors={errors} onChange={(e) => this.handleChangeDate("dateAt", e)}>*/}
                        {/*        Date de facture*/}
                        {/*    </DatePick>*/}

                        {/*    <Select items={selectDueTypes} identifiant="dueType" valeur={dueType} noEmpty={true}*/}
                        {/*            errors={errors} onChange={this.handleChange}>Conditions de paiement</Select>*/}

                        {/*    {parseInt(dueType) !== 1 && <>*/}
                        {/*        <DatePick identifiant="dueAt" valeur={dueAt} minDate={dateAt ? dateAt : new Date()}*/}
                        {/*                  errors={errors} onChange={(e) => this.handleChangeDate("dueAt", e)}>*/}
                        {/*            Date d'échéance*/}
                        {/*        </DatePick>*/}
                        {/*    </>}*/}
                        {/*</div>*/}

                        <div className="line">
                            <Radiobox items={itemsPeriod} identifiant="period" valeur={period} errors={errors} onChange={this.handleChange}>
                                Périodicité
                            </Radiobox>
                        </div>
                    </div>

                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Liste des articles</div>
                        </div>

                        <div className="line">
                            <div className="form-group">
                                <Products step={1} edit={1} noStep={true}
                                          products={products} onChangeItems={this.handleChangeItems}
                                          onSelectItem={this.handleSelectItem} item={item} items={items}
                                          element={element} societyId={societyId} taxes={taxes} unities={unities} key={i++}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Totaux</div>
                        </div>
                    </div>
                </div>

                <div className="line invoice-line invoice-line-4">
                    <div className="col-1">
                        <Tvas tvas={tvas} />
                        <div className="note">
                            <div className="line">
                                <TextArea valeur={note} identifiant="note" errors={errors} onChange={this.handleChange}>Note</TextArea>
                            </div>
                        </div>
                    </div>
                    <div className="col-2">
                        <Totaux totalHt={totalHt} totalRemise={totalRemise} totalTva={totalTva} totalTtc={totalTtc} />
                    </div>
                </div>

                <div className="invoice-line invoice-line-5">
                    <div className="note">
                        <div className="line">
                            <TextArea valeur={footer} identifiant="footer" errors={errors} onChange={this.handleChange}>
                                Note
                            </TextArea>
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? TXT_CREATE_BUTTON_FORM : TXT_UPDATE_BUTTON_FORM}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}

export class NumeroRelationForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            numero: props.numero ? props.numero : "",
            errors: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { element } = this.props;
        const { numero } = this.state;

        this.setState({ errors: [] })

        let paramsToValidate = [
            {type: "text", id: 'numero', value: numero},
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            axios({ method: "PUT", url: Routing.generate(API_NUMERO_RELATION, {'id': element.relationId}), data: this.state })
                .then(function (response) {
                    let data = response.data;
                    toastr.info("Numéro mis à jour.");

                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data, "update");
                    }
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(function() {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { errors, numero } = this.state;

        return <>
            <Input valeur={numero} identifiant="numero" errors={errors} onChange={this.handleChange}
                   placeholder={"numéro"} />
            <ButtonIcon icon="add-square" onClick={this.handleSubmit} tooltipWidth={48}>Valider</ButtonIcon>
        </>
    }
}