import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input }               from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Helper                  from "@commonComponents/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_bill_items_create";
const URL_UPDATE_GROUP       = "api_bill_items_update";
const TXT_CREATE_BUTTON_FORM = "Enregistrer";
const TXT_UPDATE_BUTTON_FORM = "Enregistrer les modifications";

let arrayZipcodes = [];

export function CustomerFormulaire ({ type, onChangeContext, onUpdateList, element, societyId })
{
    let title = "Ajouter un client";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitations ! Vous avez ajouté un nouveau client !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitations ! La mise à jour s'est réalisée avec succès !";
    }

    let form = <Form
        context={type}
        url={url}

        societyId={societyId}

        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        numeroTva={element ? Formulaire.setValueEmptyIfNull(element.numeroTva) : ""}
        email={element ? Formulaire.setValueEmptyIfNull(element.email) : ""}
        phone={element ? Formulaire.setValueEmptyIfNull(element.phone) : ""}
        address={element ? Formulaire.setValueEmptyIfNull(element.address) : ""}
        complement={element ? Formulaire.setValueEmptyIfNull(element.complement) : ""}
        zipcode={element ? Formulaire.setValueEmptyIfNull(element.zipcode) : ""}
        city={element ? Formulaire.setValueEmptyIfNull(element.city) : ""}
        country={element ? Formulaire.setValueEmptyIfNull(element.country, "France") : "France"}

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
            name: props.name,
            numeroTva: props.numeroTva,
            email: props.email,
            phone: props.phone,
            address: props.address,
            complement: props.complement,
            zipcode: props.zipcode,
            city: props.city,
            country: props.country,
            errors: [],
            success: false,
            arrayPostalCode: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        Helper.getPostalCodes(this);
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }

    handleChangeZipcodeCity = (e) => {
        const { arrayPostalCode } = this.state;

        Helper.setCityFromZipcode(this, e, arrayPostalCode ? arrayPostalCode : arrayZipcodes)
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name } = this.state;

        let method = context === "create" ? "POST" : "PUT";

        this.setState({ errors: [], success: false })

        let paramsToValidate = [
            {type: "text", id: 'name', value: name}
        ];

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
                    Helper.toTop();
                    if(self.props.onUpdateList){
                        self.props.onUpdateList(data);
                    }
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            name: "",
                            numeroTva: "",
                            email: "",
                            phone: "",
                            address: "",
                            complement: "",
                            zipcode: "",
                            city: "",
                            country: "France",
                        })
                    }
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
        const { context } = this.props;
        const { errors, success, name, numeroTva, email, phone, address, complement, zipcode, city, country } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Informations générales</div>
                        </div>

                        <div className="line line-2">
                            <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>* Nom</Input>
                            <Input valeur={numeroTva} identifiant="numeroTva" errors={errors} onChange={this.handleChange}>Numéro TVA EU</Input>
                        </div>

                        <div className="line line-2">
                            <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email">Email</Input>
                            <Input valeur={phone} identifiant="phone" errors={errors} onChange={this.handleChange}>Téléphone</Input>
                        </div>
                    </div>
                </div>

                <div className="line">

                    <div className="form-group">
                        <div className="line-separator">
                            <div className="title">Adresse postal</div>
                        </div>

                        <div className="line">
                            <Input identifiant="address" valeur={address} errors={errors} onChange={this.handleChange}>Adresse</Input>
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
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? TXT_CREATE_BUTTON_FORM : TXT_UPDATE_BUTTON_FORM}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}
