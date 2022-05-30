import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Checkbox, Input, Select, SelectReactSelectize } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button, ButtonIcon }  from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_bill_sites_create";
const URL_UPDATE_GROUP       = "api_bill_sites_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];
let i = 0;

export function SiteFormulaire ({ type, onChangeContext, onUpdateList, element, customers, societyId,
                                    customer="", yearSite, counterSite })
{
    let title = "Ajouter un site";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouveau site !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let today = new Date();
    let numero = helper.guessNumero("ST", parseInt(yearSite), today.getFullYear(), parseInt(counterSite));

    let form = <Form
        context={type}
        url={url}

        societyId={societyId}
        customer={element ? (element.customer ? element.customer.id : customer) : customer}
        customers={customers}

        useNumero={element ? (element.useNumero ? [1] : [0]) : [1]}
        numero={element ? Formulaire.setValueEmptyIfNull(element.numero, numero) : numero}
        payType={element ? Formulaire.setValueEmptyIfNull(element.payType, 0) : 0}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        email={element ? Formulaire.setValueEmptyIfNull(element.email) : ""}
        phone={element ? Formulaire.setValueEmptyIfNull(element.phone) : ""}
        address={element ? Formulaire.setValueEmptyIfNull(element.address) : ""}
        address2={element ? Formulaire.setValueEmptyIfNull(element.address2) : ""}
        complement={element ? Formulaire.setValueEmptyIfNull(element.complement) : ""}
        zipcode={element ? Formulaire.setValueEmptyIfNull(element.zipcode) : ""}
        city={element ? Formulaire.setValueEmptyIfNull(element.city) : ""}
        country={element ? Formulaire.setValueEmptyIfNull(element.country, "France") : "France"}

        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
        key={i++}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            customer: props.customer,
            useNumero: props.useNumero,
            numero: props.numero,
            payType: props.payType,
            name: props.name,
            email: props.email,
            phone: props.phone,
            address: props.address,
            address2: props.address2,
            complement: props.complement,
            zipcode: props.zipcode,
            city: props.city,
            country: props.country,
            test: "",
            errors: [],
            success: false,
            arrayPostalCode: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
        this.handleChangeSelect = this.handleChangeSelect.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
    }

    handleChange = (e) => {
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "useNumero"){
            value = e.currentTarget.checked ? [parseInt(value)] : [0]
        }

        this.setState({[name]: value})
    }

    handleChangeZipcodeCity = (e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes)
    }

    handleChangeSelect = (name, e) => { this.setState({ [name]: e !== undefined ? e.value : "" }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { typeForm, context, url, messageSuccess } = this.props;
        const { numero, name, customer, address, address2, complement, zipcode, city, country } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name', value: name},
            {type: "text", id: 'numero', value: numero},
            {type: "text", id: 'customer', value: customer},
            {type: "text", id: 'address', value: address},
            {type: "text", id: 'zipcode', value: zipcode},
            {type: "text", id: 'city', value: city},
            {type: "text", id: 'country', value: country},
        ];

        paramsToValidate = helper.checkLength(paramsToValidate, "address", address);
        paramsToValidate = helper.checkLength(paramsToValidate, "address2", address2);
        paramsToValidate = helper.checkLength(paramsToValidate, "complement", complement);

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate);
        }else{
            Formulaire.loader(true);
            let self = this;

            arrayZipcodes = this.state.arrayPostalCode;
            delete this.state.arrayPostalCode;

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
                    Formulaire.displayErrors(self, error);
                })
                .then(() => {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    render () {
        const { context, customers } = this.props;

        return <ClassiqueForm {...this.state} context={context} customers={customers}
                              onChange={this.handleChange} onChangeZipcodeCity={this.handleChangeZipcodeCity}
                              onChangeSelect={this.handleChangeSelect} onSubmit={this.handleSubmit} />
    }
}

function ClassiqueForm (props) {
    const { context, errors, success, onSubmit, onChange, onChangeZipcodeCity, onChangeSelect, customers,
        useNumero, numero, payType, name, email, phone, address, address2, complement, zipcode, city, country, customer } = props;


    let itemsCustomers = [];
    customers.forEach(customer => {
        itemsCustomers.push({ value: customer.id, label: customer.numero + " - " + customer.name, identifiant: "customer-" + customer.id })
    })

    let selectPayTypes = helper.getModePaiementChoices();
    let switcherItems = [ { value: 1, label: 'Oui', identifiant: 'oui' } ];

    return <>
        <form onSubmit={onSubmit}>

            {success !== false && <Alert type="info">{success}</Alert>}

            <div className="line line-2">

                <div className="form-group">
                    <div className="line-separator">
                        <div className="title">Informations générales</div>
                    </div>

                    <div className="line line-2">
                        {parseInt(useNumero) === 1 ? <>
                            <div className="form-group">
                                <label>* Numéro</label>
                                <div>{numero}</div>
                            </div>
                        </> : <>
                            <Input valeur={numero} identifiant="numero" errors={errors} onChange={onChange}>* Numéro</Input>
                        </>}
                        {context === "create" ? <>
                            <Checkbox isSwitcher={true} items={switcherItems} identifiant="useNumero" valeur={useNumero} errors={errors} onChange={onChange}>
                                Numérotation par défaut ?
                            </Checkbox>
                        </> : <div className="form-group" />}
                    </div>

                    <div className="line line-2">
                        <Input valeur={name} identifiant="name" errors={errors} onChange={onChange}>* Designation</Input>
                        <div className="form-group" />
                    </div>

                    <div className="line line-2">
                        <Input valeur={email} identifiant="email" errors={errors} onChange={onChange} type="email">Email</Input>
                        <Input valeur={phone} identifiant="phone" errors={errors} onChange={onChange}>Téléphone</Input>
                    </div>
                    <div className="line line-2">
                        <Select items={selectPayTypes} identifiant="payType" valeur={payType} noEmpty={true} errors={errors} onChange={onChange}>
                            Mode de règlement
                        </Select>
                        <div className="form-group" />
                    </div>
                </div>

                <div className="form-group">
                    <div className="line-separator">
                        <div className="title">Adresse postal</div>
                    </div>

                    <div className="line">
                        <Input identifiant="address" valeur={address} errors={errors} onChange={onChange}>* Adresse</Input>
                    </div>
                    <div className="line">
                        <Input identifiant="address2" valeur={address2} errors={errors} onChange={onChange}>Adresse ligne 2</Input>
                    </div>
                    <div className="line">
                        <Input identifiant="complement" valeur={complement} errors={errors} onChange={onChange}>Complément d'adresse</Input>
                    </div>
                    <div className="line line-3">
                        <Input identifiant="zipcode" valeur={zipcode} errors={errors} onChange={onChangeZipcodeCity} type="number">* Code postal</Input>
                        <Input identifiant="city" valeur={city} errors={errors} onChange={onChange}>* Ville</Input>
                        <Input identifiant="country" valeur={country} errors={errors} onChange={onChange}>* Pays</Input>
                    </div>

                    <div className="line-separator">
                        <div className="title">Client associé</div>
                    </div>

                    <div className="line">
                        <SelectReactSelectize items={itemsCustomers} identifiant="customer" placeholder={"Sélectionner le client"}
                                              valeur={customer} errors={errors} onChange={(e) => onChangeSelect('customer', e)}>
                            * Client
                        </SelectReactSelectize>
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

export class CustomerSelectForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            site: "",
            errors: []
        }

        this.handleChangeSelect = this.handleChangeSelect.bind(this);
        this.handleSelectSite = this.handleSelectSite.bind(this);
    }

    handleChangeSelect = (name, e) => { this.setState({ [name]: e !== undefined ? e.value : "" }) }

    handleSelectSite = () => {
        const { sites } = this.props;
        const { site } = this.state;

        if(site !== ""){
            let s = null
            sites.forEach(si => {
                if(si.uid === site){
                    s = si;
                }
            })

            if(s){
                this.props.onAssociateSite(s);
            }
        }
    }

    render () {
        const { sites } = this.props;
        const { site, errors } = this.state;

        let itemsSites = [];
        sites.forEach(si => {
            if(si.customer === null){
                itemsSites.push({ value: si.uid, label: si.name, identifiant: 'si-' + si.uid })
            }
        })

        return <>
            <div className="item item-main">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-2">
                            <div className="col-1">
                                <div className="line line-2">
                                    <div className="name">Sélectionner un site existant</div>
                                </div>
                            </div>
                            <div className="col-2 actions"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="item item-add">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-3">
                            <div className="col-1">
                                <SelectReactSelectize items={itemsSites} identifiant="site" placeholder={"Sélectionner un site à associer"}
                                                      valeur={site} errors={errors} onChange={(e) => this.handleChangeSelect('site', e)} />
                            </div>
                            <div className="col-2" />
                            <div className="col-3 actions">
                                <ButtonIcon icon="user-add" onClick={this.handleSelectSite}>Associer</ButtonIcon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}

function resetForm (self) {
    self.setState( {
        name: "",
        address: "",
        address2: "",
        complement: "",
        zipcode: "",
        city: "",
        country: "France",
    })
}