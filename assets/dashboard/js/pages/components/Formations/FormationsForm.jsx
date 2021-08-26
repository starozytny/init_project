import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Checkbox }     from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { Drop }                from "@dashboardComponents/Tools/Drop";

import Validateur              from "@dashboardComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

export function FormationsFormulaire ({ type, onChangeContext, onUpdateList, element })
{
    let title = "Ajouter une formations";
    let url = Routing.generate('api_users_create');
    let msg = "Félicitation ! Vous avez ajouté une nouvelle formation !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate('api_users_update', {'id': element.id});
        msg = "Félicitation ! La mise à jour s'est réalisé avec succès !";
    }

    let form = <FormationsForm
        context={type}
        url={url}
        name={element ? element.name : ""}
        content={element ? element.content : ""}
        price={element ? element.price : ""}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class FormationsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: props.name,
            content: props.content,
            price: props.price,
            errors: [],
            success: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        document.getElementById("name").focus()
    }

    handleChange = (e) => {
        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        this.setState({[name]: value})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name, content, price } = this.state;

        this.setState({ success: false})

        let paramsToValidate = [
            {type: "text", id: 'username', value: name},
            {type: "text", id: 'firstname', value: content},
            {type: "text", id: 'lastname', value: price},
        ];

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            toastr.warning("Veuillez vérifier les informations transmises.");
            this.setState({ errors: validate.errors });
        }else{
            Formulaire.loader(true);
            let self = this;

            let formData = new FormData();
            formData.append("data", JSON.stringify(this.state));

            axios({ method: "POST", url: url, data: formData, headers: {'Content-Type': 'multipart/form-data'} })
                .then(function (response) {
                    let data = response.data;
                    self.props.onUpdateList(data);
                    self.setState({ success: messageSuccess, errors: [] });
                    if(context === "create"){
                        self.setState( {
                            name: '',
                            content: '',
                            price: '',
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
        const { errors, success, name, content, price } = this.state;

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line line-2">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange} >Intitulé</Input>
                    <Input valeur={price} identifiant="price" errors={errors} onChange={this.handleChange} type="number" >Prix</Input>
                </div>

                <div className="line">
                    <div className="form-button">
                        <Button isSubmit={true}>{context === "create" ? "Ajouter la formation" : 'Modifier la formation'}</Button>
                    </div>
                </div>
            </form>
        </>
    }
}