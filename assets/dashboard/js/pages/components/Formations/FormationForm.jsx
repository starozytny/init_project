import React, { Component } from 'react';

import axios                   from "axios";
import toastr                  from "toastr";
import Routing                 from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Input, Select }       from "@dashboardComponents/Tools/Fields";
import { Alert }               from "@dashboardComponents/Tools/Alert";
import { Button }              from "@dashboardComponents/Tools/Button";
import { Trumb }               from "@dashboardComponents/Tools/Trumb";
import { FormLayout }          from "@dashboardComponents/Layout/Elements";

import Validateur              from "@commonComponents/functions/validateur";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

const URL_CREATE_ELEMENT     = "api_formations_create";
const URL_UPDATE_GROUP       = "api_formations_update";
const TXT_CREATE_BUTTON_FORM = "Ajouter la formation";
const TXT_UPDATE_BUTTON_FORM = "Modifier la formation";

export function FormationsFormulaire ({ type, onChangeContext, onUpdateList, element })
{
    let title = "Ajouter une formation";
    let url = Routing.generate(URL_CREATE_ELEMENT);
    let msg = "Félicitation ! Vous avez ajouté une nouvelle formation !"

    if(type === "update"){
        title = "Modifier " + element.name;
        url = Routing.generate(URL_UPDATE_GROUP, {'id': element.id});
        msg = "Félicitation ! La mise à jour s'est réalisé avec succès !";
    }

    let form = <FormationForm
        context={type}
        url={url}
        name={element ? element.name : ""}
        content={element ? element.content : ""}
        prerequis={element ? element.prerequis : ""}
        goals={element ? element.goals : ""}
        aptitudes={element ? element.aptitudes : ""}
        skills={element ? element.skills : ""}
        target={element ? element.target : ""}
        cat={element ? element.cat : ""}
        accessibility={element ? element.accessibility : 0}
        onUpdateList={onUpdateList}
        onChangeContext={onChangeContext}
        messageSuccess={msg}
    />

    return <FormLayout onChangeContext={onChangeContext} form={form}>{title}</FormLayout>
}

export class FormationForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: props.name,
            price: props.price,
            content: { value: props.content ? props.content : "", html: props.content ? props.content : "" },
            prerequis: { value: props.prerequis ? props.prerequis : "", html: props.prerequis ? props.prerequis : "" },
            goals: { value: props.goals ? props.goals : "", html: props.goals ? props.goals : "" },
            aptitudes: { value: props.aptitudes ? props.aptitudes : "", html: props.aptitudes ? props.aptitudes : "" },
            skills: { value: props.skills ? props.skills : "", html: props.skills ? props.skills : "" },
            target: { value: props.target ? props.target : "", html: props.target ? props.target : "" },
            cat: { value: props.cat ? props.cat : "", html: props.cat ? props.cat : "" },
            accessibility: props.accessibility,
            errors: [],
            success: false
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleChangeTrumb = this.handleChangeTrumb.bind(this);
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

    handleChangeTrumb = (e) => {
        const { content, prerequis, goals, aptitudes, skills, target, cat } = this.state

        let name = e.currentTarget.id;
        let text = e.currentTarget.innerHTML;
        let el;
        switch (name) {
            case "cat":
                el = cat;
                break;
            case "target":
                el = target;
                break;
            case "skills":
                el = skills;
                break;
            case "aptitudes":
                el = aptitudes;
                break;
            case "goals":
                el = goals;
                break;
            case "prerequis":
                el = prerequis;
                break;
            default:
                el = content;
                break;
        }

        this.setState({[name]: {value: el.value, html: text}})
    }

    handleSubmit = (e) => {
        e.preventDefault();

        const { context, url, messageSuccess } = this.props;
        const { name } = this.state;

        this.setState({ success: false })

        let paramsToValidate = [
            {type: "text", id: 'name',  value: name}
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
                            content: { value: "", html: "" },
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
        const { errors, success, name, content, prerequis, goals, aptitudes, skills, target, cat, accessibility } = this.state;

        let selectItems = [
            { value: 0, label: 'Bâtiment non conforme', identifiant: 'bat-not-conforme' },
            { value: 1, label: 'Bâtiment conforme', identifiant: 'bat-conforme' },
        ]

        return <>
            <form onSubmit={this.handleSubmit}>

                {success !== false && <Alert type="info">{success}</Alert>}

                <div className="line">
                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange} >Intitulé</Input>
                </div>
                <div className="line line-2">
                    <Select items={selectItems} identifiant="accessibility" valeur={accessibility} errors={errors} onChange={this.handleChange} noEmpty={true}>Accessibilité handicapé ?</Select>
                    <div className="form-group" />
                </div>

                <div className="line">
                    <Trumb identifiant="content" valeur={content.value} errors={errors} onChange={this.handleChangeTrumb}>Description</Trumb>
                </div>

                <div className="line line-2">
                    <Trumb identifiant="prerequis" valeur={prerequis.value} errors={errors} onChange={this.handleChangeTrumb}>Prérequis</Trumb>
                    <Trumb identifiant="goals" valeur={goals.value} errors={errors} onChange={this.handleChangeTrumb}>Objectifs</Trumb>
                </div>

                <div className="line line-2">
                    <Trumb identifiant="aptitudes" valeur={aptitudes.value} errors={errors} onChange={this.handleChangeTrumb}>Aptitudes</Trumb>
                    <Trumb identifiant="skills" valeur={skills.value} errors={errors} onChange={this.handleChangeTrumb}>Compétences</Trumb>
                </div>

                <div className="line line-2">
                    <Trumb identifiant="target" valeur={target.value} errors={errors} onChange={this.handleChangeTrumb}>Public cible</Trumb>
                    <Trumb identifiant="cat" valeur={cat.value} errors={errors} onChange={this.handleChangeTrumb}>Catégorie de formation</Trumb>
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