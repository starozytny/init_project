import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, TextArea }     from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";
import { RgpdInfo }            from "@appComponents/Tools/Rgpd";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function DemandeFormulaire ({ type, onChangeContext, onUpdateList, bien })
{
    let title = "Ajouter une demande";
    let url = Routing.generate('api_immo_demandes_create');
    let msg = "Félicitation ! Vous avez ajouté une demande !"

    let form = <DemandeForm
        context={type}
        url={url}
        bien={bien} // to change
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class DemandeForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: "",
            email: "",
            phone: "",
            message: "Bonjour,\n" +
                "                    \n" +
                "je souhaiterais être recontacté.\n" +
                "\n" +
                "Cordialement,",
            bien: props.bien,
            errors: [],
            success: false,
            critere: ""
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("name").focus()
    }

    handleChange = (e) => { this.setState({[e.currentTarget.name]: e.currentTarget.value}) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { critere, name, email, message } = this.state;

        if(critere !== ""){
            toastr.error("Veuillez rafraichir la page.");
        }else{
            this.setState({ success: false})

            let method = "POST";
            let paramsToValidate = [
                {type: "text", id: 'name', value: name},
                {type: "text", id: 'email', value: email},
                {type: "text", id: 'message', value: message}
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
                            document.body.scrollTop = 0; // For Safari
                            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera

                            toastr.info(messageSuccess);
                            self.setState( {
                                name: '',
                                email: '',
                                phone: '',
                                message: '',
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
    }

    render () {
        const { context } = this.props;
        const { critere, errors, success, name, email, phone, message } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange}>Nom / raison sociale</Input>
                </div>

                <div className="line">
                    <Input valeur={email} identifiant="email" errors={errors} onChange={this.handleChange} type="email" >Adresse e-mail</Input>
                </div>

                <div className="line line-critere">
                    <Input identifiant="critere" valeur={critere} errors={errors} onChange={this.handleChange}>Critère</Input>
                </div>

                <div className="line">
                    <Input valeur={phone} identifiant="phone" errors={errors} onChange={this.handleChange}>Téléphone (facultatif)</Input>
                </div>

                <div className="line">
                    <TextArea valeur={message} identifiant="message" errors={errors} onChange={this.handleChange}>Message</TextArea>
                </div>

                <div className="line">
                    <RgpdInfo utility="la gestion des demandes d'informations"/>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? "Ajouter une demande" : 'Modifier la demande'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}