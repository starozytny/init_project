import React, { Component } from "react";

import axios   from "axios";
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, SelectReactSelectize } from "@dashboardComponents/Tools/Fields";
import { DatePick }   from "@dashboardComponents/Tools/DatePicker";
import { Button }     from "@dashboardComponents/Tools/Button";

import Formulaire from "@dashboardComponents/functions/Formulaire";
import Validateur from "@commonComponents/functions/validateur";

import { Invoices }  from "@dashboardPages/components/Bill/Invoice/Invoices";
import { Societies } from "@dashboardPages/components/Bill/Society/Societies";

const URL_UPDATE_SETTINGS   = "api_bill_societies_update_settings";
const URL_GENERATE_SOCIETY  = "api_bill_general_generate_society";
const URL_GENERAL_INFOS     = "api_bill_general_data";
const URL_INVOICE_INFOS     = "api_bill_invoices_data";
const URL_SOCIETY_INFOS     = "api_bill_societies_data";

let i = 0;

export class Dashboard extends Component {
    constructor(props) {
        super(props);

        this.state = {
            context: 'general',
            data: props.donnees ? JSON.parse(props.donnees) : [],

            biSociety: null,

            invoices: [],
            societies: [],

            customers: [],
            items: [],
            products: [],
            sites: [],
            society: [],
            taxes: [],
            unities: [],

            societyId: "",

            errors: []
        }

        this.handleChangeContext = this.handleChangeContext.bind(this);
        this.handleChangeSelect = this.handleChangeSelect.bind(this);
        this.handleGenerateSociety = this.handleGenerateSociety.bind(this);
    }

    handleChangeContext = (context, nameUrl) => {
        const { societyId } = this.state;

        if(societyId !== ""){
            let self = this;
            Formulaire.loader(true);
            axios({ method: "GET", url: Routing.generate(nameUrl, {'id': societyId}), data: {} })
                .then(function (response) {
                    let data = response.data;

                    setData(self, context, data);
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(() => {
                    Formulaire.loader(false);
                })
            ;
        }

        this.setState({ context })
    }

    handleChangeSelect = (name, e) => {
        this.setState({
            invoices: [],
            societies: [],
            customers: [],
            items: [],
            products: [],
            sites: [],
            society: [],
            taxes: [],
            unities: [],
        })

        if(e !== undefined){
            let id = e.value;

            let self = this;
            Formulaire.loader(true);
            axios({ method: "GET", url: Routing.generate(URL_GENERAL_INFOS, {'id': id}), data: {} })
                .then(function (response) {
                    let data = response.data;

                    self.setState({ [name]: id, biSociety: data })
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(() => {
                    Formulaire.loader(false);
                })
            ;
        }else{
            this.setState({ [name]: "" })
        }
    }

    handleGenerateSociety = () => {
        const { societyId } = this.state;

        if(societyId){
            let self = this;
            Formulaire.loader(true);
            axios({ method: "GET", url: Routing.generate(URL_GENERATE_SOCIETY, {'id': societyId}), data: {} })
                .then(function (response) {
                    let data = response.data;

                    self.setState({ biSociety: data })
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
        const { context, data, errors, societyId, biSociety,
            invoices, societies,
            customers, items, products, sites, society, taxes, unities } = this.state;

        let element = "";
        let selectSociety = [];
        data.forEach(elem => {
            selectSociety.push({ value: elem.id, label: "#" + elem.code + " - " + elem.name, identifiant: "soc-" + elem.id });

            if(elem.id === societyId){
                element = elem;
            }
        })

        let content = "";
        switch (context){
            case "society":
                content = <div id="societies">
                    <Societies donnees={JSON.stringify(societies)} classes=" " key={i++} />
                </div>
                break;
            case "invoice":
                content = <div id="invoices" className="invoice-form">
                    <Invoices donnees={JSON.stringify(invoices)} customers={JSON.stringify(customers)}
                              items={JSON.stringify(items)} products={JSON.stringify(products)}
                              sites={JSON.stringify(sites)} society={JSON.stringify(society)}
                              taxes={JSON.stringify(taxes)} unities={JSON.stringify(unities)} classes=" " key={i++} />
                </div>
                break;
            default:
                content = biSociety ? <General societyId={societyId} biSociety={biSociety} /> : <Button onClick={this.handleGenerateSociety}>Générer la société</Button>;
                break;
        }

        return <div className="main-content">
            <div className="societies">
                <div className="col-1">
                    <div className="form">
                        <div className="line">
                            <SelectReactSelectize items={selectSociety} identifiant="societyId" valeur={societyId}
                                                  placeholder={"Sélectionner une société"}
                                                  errors={errors} onChange={(e) => this.handleChangeSelect("societyId", e)}
                            >
                                Société
                            </SelectReactSelectize>
                        </div>
                    </div>
                </div>
                <div className="col-2">
                    {element && <>
                        <div className="societies-infos">
                            <div><b>{element.name}</b></div>
                            <div className="badge badge-0">#{element.codeString}</div>
                            <div>Identifiant manager : {element.manager}</div>
                        </div>
                    </>}
                </div>
            </div>

            {societyId && <>
                <div className="societies-data">
                    <div className="societies-menu submenu">
                        <Menu context={context} onChangeContext={this.handleChangeContext} />
                    </div>
                    <div className="societies-content">
                        {content}
                    </div>
                </div>
            </>}
        </div>
    }
}

function Menu ({ context, onChangeContext }) {
    let items = [
        { value: 'general',     label: "Général",   nameUrl: URL_GENERAL_INFOS },
        { value: 'society',     label: "Sociétés",  nameUrl: URL_SOCIETY_INFOS },
        { value: 'devis',       label: "Devis",     nameUrl: URL_GENERAL_INFOS },
        { value: 'invoice',     label: "Factures",  nameUrl: URL_INVOICE_INFOS },
        { value: 'contract',    label: "Contrats",  nameUrl: URL_GENERAL_INFOS },
        { value: 'customer',    label: "Clients",   nameUrl: URL_GENERAL_INFOS },
        { value: 'items',       label: "Articles",  nameUrl: URL_GENERAL_INFOS },
    ]
    return <div className="submenu-container">
        {items.map((el, index) => {
            return <a className={el.value === context ? "active" : ""} key={index}
                      onClick={() => onChangeContext(el.value, el.nameUrl)}
            >{el.label}</a>
        })}
    </div>
}

function setData(self, type, data)
{
    switch (type){
        case "society":
            let societies = JSON.parse(data.societies)

            self.setState({ societies })
            break;
        case "invoice":
            let invoices = JSON.parse(data.invoices)

            let customers = JSON.parse(data.customers)
            let items = JSON.parse(data.items)
            let products = JSON.parse(data.products)
            let sites = JSON.parse(data.sites)
            let society = JSON.parse(data.society)
            let taxes = JSON.parse(data.taxes)
            let unities = JSON.parse(data.unities)

            self.setState({ invoices, customers, items, products, sites, society, taxes, unities })
            break;
        default:
            break;
    }
}

class General extends Component {
    constructor(props) {
        super(props);

        let element = props.biSociety;

        this.state = {
            counterInvoice: element ? Formulaire.setValueEmptyIfNull(element.counterInvoice) : 0,
            counterQuotation: element ? Formulaire.setValueEmptyIfNull(element.counterQuotation) : 0,
            counterContract: element ? Formulaire.setValueEmptyIfNull(element.counterContract) : 0,
            counterCustomer: element ? Formulaire.setValueEmptyIfNull(element.counterCustomer) : 0,
            counterAvoir: element ? Formulaire.setValueEmptyIfNull(element.counterAvoir) : 0,
            yearInvoice: element ? Formulaire.setValueEmptyIfNull(element.yearInvoice) : 2022,
            yearQuotation: element ? Formulaire.setValueEmptyIfNull(element.yearQuotation) : 2022,
            yearContract: element ? Formulaire.setValueEmptyIfNull(element.yearContract) : 2022,
            yearCustomer: element ? Formulaire.setValueEmptyIfNull(element.yearCustomer) : 2022,
            yearAvoir: element ? Formulaire.setValueEmptyIfNull(element.yearAvoir) : 2022,
            dateInvoice: element ? Formulaire.setDateOrEmptyIfNull(element.dateInvoiceJavascript) : "",
            dateContract: element ? Formulaire.setDateOrEmptyIfNull(element.dateContractJavascript) : "",
            errors: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeDate = this.handleChangeDate.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleChangeDate = (name, e) => { this.setState({ [name]: e !== null ? e : "" }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { societyId, biSociety } = this.props;
        const { yearInvoice, yearQuotation, yearContract, yearCustomer, yearAvoir,
            counterInvoice, counterQuotation, counterContract, counterCustomer, counterAvoir, dateContract } = this.state;

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "date", id: 'dateContract',      value: dateContract},
            {type: "text", id: 'yearInvoice',       value: yearInvoice},
            {type: "text", id: 'yearQuotation',     value: yearQuotation},
            {type: "text", id: 'yearContract',      value: yearContract},
            {type: "text", id: 'yearCustomer',      value: yearCustomer},
            {type: "text", id: 'yearAvoir',         value: yearAvoir},
            {type: "text", id: 'counterInvoice',    value: counterInvoice},
            {type: "text", id: 'counterQuotation',  value: counterQuotation},
            {type: "text", id: 'counterContract',   value: counterContract},
            {type: "text", id: 'counterCustomer',   value: counterCustomer},
            {type: "text", id: 'counterAvoir',      value: counterAvoir},
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            const self = this;
            Formulaire.loader(true);
            axios({ method: "PUT", url: Routing.generate(URL_UPDATE_SETTINGS, {'society': societyId, 'id': biSociety.id}), data: this.state })
                .then(function (response) {
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
        const { errors,
            yearInvoice, yearQuotation, yearContract, yearCustomer, yearAvoir,
            counterInvoice, counterQuotation, counterContract, counterCustomer, counterAvoir,
            dateInvoice, dateContract
        } = this.state;

        return <form onSubmit={this.handleSubmit}>
            <div className="line line-3">
                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={yearInvoice} identifiant="yearInvoice" errors={errors} onChange={this.handleChange}>
                            Année factures
                        </Input>
                        <Input valeur={counterInvoice} identifiant="counterInvoice" errors={errors} onChange={this.handleChange}>
                            Compteur factures
                        </Input>
                    </div>
                </div>

                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={yearQuotation} identifiant="yearQuotation" errors={errors} onChange={this.handleChange}>
                            Année devis
                        </Input>
                        <Input valeur={counterQuotation} identifiant="counterQuotation" errors={errors} onChange={this.handleChange}>
                            Compteur devis
                        </Input>
                    </div>
                </div>

                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={yearAvoir} identifiant="yearAvoir" errors={errors} onChange={this.handleChange}>
                            Année avoirs
                        </Input>
                        <Input valeur={counterAvoir} identifiant="counterAvoir" errors={errors} onChange={this.handleChange}>
                            Compteur avoirs
                        </Input>
                    </div>
                </div>
            </div>

            <div className="line line-3">
                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={yearContract} identifiant="yearContract" errors={errors} onChange={this.handleChange}>
                            Année contrats
                        </Input>
                        <Input valeur={counterContract} identifiant="counterContract" errors={errors} onChange={this.handleChange}>
                            Compteur contrats
                        </Input>
                    </div>
                </div>

                <div className="form-group">
                    <div className="line line-2">
                        <Input valeur={yearCustomer} identifiant="yearCustomer" errors={errors} onChange={this.handleChange}>
                            Année clients
                        </Input>
                        <Input valeur={counterCustomer} identifiant="counterCustomer" errors={errors} onChange={this.handleChange}>
                            Compteur clients
                        </Input>
                    </div>
                </div>

                <div className="form-group" />
            </div>


            <div className="line line-3">
                <div className="form-group">
                    <DatePick identifiant="dateInvoice" valeur={dateInvoice} errors={errors} onChange={(e) => this.handleChangeDate("dateInvoice", e)}>
                        Dernière date facture
                    </DatePick>
                </div>
                <div className="form-group">
                    <DatePick identifiant="dateContract" valeur={dateContract} errors={errors} onChange={(e) => this.handleChangeDate("dateContract", e)}>
                        Dernière date contrat
                    </DatePick>
                </div>
                <div className="form-group" />
            </div>

            <div className="line-buttons">
                <div className="form-button">
                    <Button isSubmit={true}>Enregistrer</Button>
                </div>
            </div>
        </form>
    }
}