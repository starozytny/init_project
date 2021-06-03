import React, { Component } from 'react';

import axios                   from "axios";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import {Input, Checkbox, Radiobox} from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function AlertFormulaire ({ type, onChangeContext, onUpdateList })
{
    let title = "Ajouter une alerte";
    let url = Routing.generate('api_immo_alerts_create');
    let msg = "Félicitation ! Vous avez ajouté une alerte !"

    let form = <AlertForm
        context={type}
        url={url}
        email=""
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class AlertForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            email: props.email,
            typeAd: "",
            typeBiens: [],
            errors: [],
            success: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("email").focus()
    }

    handleChange = (e) => {
        const { typeBiens } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "typeBiens"){
            value = (e.currentTarget.checked) ? [...typeBiens, ...[value]] : typeBiens.filter(v => v !== value)
        }
        this.setState({[name]: value})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { email, typeAd, typeBiens } = this.state;

        this.setState({ success: false})

        let method = "POST";
        let paramsToValidate = [
            {type: "text", id: 'email', value: email},
            {type: "text", id: 'typeAd', value: typeAd},
            {type: "text", id: 'typeBiens', value: typeBiens}
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            this.setState({ errors: validate.errors });
        }else{
            Formulaire.loader(true);
            let self = this;
            axios({ method: method, url: url, data: self.state })
                .then(function (response) {
                    let data = response.data;
                    self.props.onUpdateList(data);
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            email: '',
                            typeAd: '',
                            typeBien: ''
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
        const { errors, success, email, typeAd, typeBiens } = this.state;

        let naturesItems = [
            { value: 0, label: 'Location', identifiant: 'location' },
            { value: 1, label: 'Vente', identifiant: 'vente' },
        ]

        let biensItems = [
            { value: "maison", label: "Maison", identifiant: "maison" },
            { value: "appartement", label: "Appartement", identifiant: "appartement" },
            { value: "parking", label: "Parking", identifiant: "parking" },
            { value: "bureaux", label: "Bureaux", identifiant: "bureaux" },
            { value: "local", label: "Local", identifiant: "local" },
            { value: "immeuble", label: "Immeuble", identifiant: "immeuble" },
            { value: "terrain", label: "Terrain", identifiant: "terrain" },
            { value: "commerce", label: "Commerce", identifiant: "commerce" },
        ]

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">
                    <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail</Input>
                </div>

                <div className="line line-2">
                    <Radiobox items={naturesItems} identifiant="typeAd" valeur={typeAd} errors={errors} onChange={this.handleChange}>Quel est votre projet ?</Radiobox>
                    <Checkbox items={biensItems} identifiant="typeBiens" valeur={typeBiens} errors={errors} onChange={this.handleChange}>De quel bien(s) s'agit-il ?</Checkbox>
                </div>
                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? "Ajouter une alerte" : 'Modifier l\'alerte'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}