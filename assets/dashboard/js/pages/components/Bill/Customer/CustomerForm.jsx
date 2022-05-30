import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Checkbox, Input, Select } from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

import { SitesListCustomerForm } from "@dashboardPages/components/Bill/Site/SitesList";

const URL_CREATE_ELEMENT     = "api_bill_customers_create";
const URL_UPDATE_GROUP       = "api_bill_customers_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];

export function CustomerFormulaire ({ type, onChangeContext, onUpdateList, element, societyId, sites, onUpdateListSites,
                                        counterCustomer, yearCustomer })
{
    let title = "Ajouter un client";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouveau client !"
    let customerSites = [];

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";

        sites.forEach(site => {
            if(site.customer.id === element.id){
                customerSites.push(site);
            }
        })
    }

    let today = new Date();
    let numero = helper.guessNumero("CL", parseInt(yearCustomer), today.getFullYear(), parseInt(counterCustomer));

    let form = <Form
        context={type}
        url={url}

        societyId={societyId}
        sites={sites}

        id={element ? element.id : null}
        useNumero={element ? (element.useNumero ? [1] : [0]) : [1]}
        numero={element ? Formulaire.setValueEmptyIfNull(element.numero, numero) : numero}
        payType={element ? Formulaire.setValueEmptyIfNull(element.payType, 0) : 0}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        numeroTva={element ? Formulaire.setValueEmptyIfNull(element.numeroTva) : ""}
        email={element ? Formulaire.setValueEmptyIfNull(element.email) : ""}
        phone={element ? Formulaire.setValueEmptyIfNull(element.phone) : ""}
        address={element ? Formulaire.setValueEmptyIfNull(element.address) : ""}
        address2={element ? Formulaire.setValueEmptyIfNull(element.address2) : ""}
        complement={element ? Formulaire.setValueEmptyIfNull(element.complement) : ""}
        zipcode={element ? Formulaire.setValueEmptyIfNull(element.zipcode) : ""}
        city={element ? Formulaire.setValueEmptyIfNull(element.city) : ""}
        country={element ? Formulaire.setValueEmptyIfNull(element.country, "France") : "France"}
        customerSites={customerSites}

        onUpdateListSites={onUpdateListSites}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <>{onChangeContext ? <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout> : form}</>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            useNumero: props.useNumero,
            numero: props.numero,
            payType: props.payType,
            name: props.name,
            numeroTva: props.numeroTva,
            email: props.email,
            phone: props.phone,
            address: props.address,
            address2: props.address2,
            complement: props.complement,
            zipcode: props.zipcode,
            city: props.city,
            country: props.country,
            customerSites: props.customerSites,
            errors: [],
            success: false,
            arrayPostalCode: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeZipcodeCity = this.handleChangeZipcodeCity.bind(this);
        this.handleAssociateSite = this.handleAssociateSite.bind(this);
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

    handleAssociateSite = (site) => {
        const { customerSites } = this.state;

        let find = false;
        customerSites.forEach(si => {
            if(si.uid === site.uid){
                find = true;
            }
        })

        let nSites = !find ? [...customerSites, ...[site]] : customerSites;
        this.setState({ customerSites: nSites })
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name, payType, address, address2, complement } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name', value: name},
            {type: "text", id: 'payType', value: payType},
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
                    Formulaire.loader(false);
                    Formulaire.displayErrors(self, error);
                })
            ;
        }
    }

    render () {
        const { context, sites, onUpdateListSites, id } = this.props;
        const { errors, success, societyId, useNumero, numero, payType, name, numeroTva, email, phone,
            address, address2, complement, zipcode, city, country, customerSites } = this.state;

        let selectPayTypes = helper.getModePaiementChoices();
        let switcherItems = [ { value: 1, label: 'Oui', identifiant: 'oui' } ];

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Informations générales</div>
                        </div>

                        <div className="line line-2">
                            {parseInt(useNumero) === 1 ? <>
                                <div className="form-group">
                                    <label>* Numéro client</label>
                                    <div>{numero}</div>
                                </div>
                            </> : <>
                                <Input valeur={numero} identifiant="numero" errors={errors} onChange={this.handleChange}>* Numéro client</Input>
                            </>}
                            {context === "create" ? <>
                                <Checkbox isSwitcher={true} items={switcherItems} identifiant="useNumero" valeur={useNumero} errors={errors} onChange={this.handleChange}>
                                    Numérotation par défaut ?
                                </Checkbox>
                            </> : <div className="form-group" />}
                        </div>

                        <div className="line line-2">
                            <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>* Nom</Input>
                            <div className="form-group" />
                        </div>

                        <div className="line line-2">
                            <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email">Email</Input>
                            <Input valeur={phone} identifiant="phone" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                        </div>
                        <div className="line line-2">
                            <Input valeur={numeroTva} identifiant="numeroTva" errors={errors} onChange={this.handleChange}>Numéro TVA EU</Input>
                            <div className="form-group" />
                        </div>

                        <div className="line">
                            <Select items={selectPayTypes} identifiant="payType" valeur={payType} noEmpty={true} errors={errors} onChange={this.handleChange}>
                                Mode de règlement
                            </Select>
                        </div>
                    </div>

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Adresse postal</div>
                        </div>

                        <div className="line">
                            <Input identifiant="address" valeur={address} errors={errors} onChange={this.handleChange}>Adresse</Input>
                        </div>
                        <div className="line">
                            <Input identifiant="address2" valeur={address2} errors={errors} onChange={this.handleChange}>Adresse ligne 2</Input>
                        </div>
                        <div className="line">
                            <Input identifiant="complement" valeur={complement} errors={errors} onChange={this.handleChange}>Complément d'adresse</Input>
                        </div>
                        <div className="line line-3">
                            <Input identifiant="zipcode" valeur={zipcode} errors={errors} onChange={this.handleChangeZipcodeCity} type="number">Code postal</Input>
                            <Input identifiant="city" valeur={city} errors={errors} onChange={this.handleChange}>Ville</Input>
                            <Input identifiant="country" valeur={country} errors={errors} onChange={this.handleChange}>Pays</Input>
                        </div>
                    </div>
                </div>

                <div className="line">
                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Liste des sites associés</div>
                        </div>

                        <div className="line">
                            <div className="form-group">
                                <SitesListCustomerForm data={customerSites} sites={sites} societyId={societyId} customer={id}
                                                       onUpdateList={onUpdateListSites} onAssociateSite={this.handleAssociateSite}/>
                            </div>
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