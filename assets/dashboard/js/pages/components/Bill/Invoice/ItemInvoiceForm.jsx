import React, { Component } from 'react';

import { uid }  from "uid";

import { Input, SelectReactSelectize, TextArea } from "@dashboardComponents/Tools/Fields";
import { ButtonIcon } from "@dashboardComponents/Tools/Button";

import Validateur              from "@commonComponents/functions/validateur";
import Sanitaze                from "@commonComponents/functions/sanitaze";
import helper                  from "@dashboardPages/components/Bill/functions/helper";
import Formulaire              from "@dashboardComponents/functions/Formulaire";

export function ItemInvoiceFormulaire ({ element, societyId, taxes, unities, onSubmit,
                                           selectItems, item, onSelectItem, onCloseAdd, step })
{
    let form = <Form
        onSubmit={onSubmit}

        societyId={societyId}
        taxes={taxes}
        unities={unities}

        selectItems={selectItems}
        item={item}
        step={step}
        onSelectItem={onSelectItem}
        onCloseAdd={onCloseAdd}

        uid={element ? (element.uid ? element.uid :  uid(16)) : uid(16) }
        reference={element ? Formulaire.setValueEmptyIfNull(element.reference) : ""}
        numero={element ? Formulaire.setValueEmptyIfNull(element.numero) : ""}
        name={element ? Formulaire.setValueEmptyIfNull(element.name) : ""}
        content={element ? Formulaire.setValueEmptyIfNull(element.content) : ""}
        unity={element ? Formulaire.setValueEmptyIfNull(element.unity) : "pièce"}
        price={element ? Formulaire.setToFloat(element.price) : ""}
        quantity={element && element.quantity ? Formulaire.setToFloat(element.quantity, 1) : 1}
        rateTva={element ? Formulaire.setValueEmptyIfNull(element.rateTva, 20) : 20}
        codeTva={element ? Formulaire.setValueEmptyIfNull(element.codeTva, 1) : 1}
    />

    return <>{form}</>
}

class Form extends Component {
    constructor(props) {
        super(props);

        this.state = {
            societyId: props.societyId,
            uid: props.uid,
            reference: props.reference,
            numero: props.numero,
            name: props.name,
            content: props.content,
            unity: props.unity,
            price: props.price,
            rateTva: props.rateTva,
            codeTva: props.codeTva,
            quantity: props.quantity,
            totalHt: props.quantity !== 0 ? props.quantity * (props.price !== "" ? parseFloat(props.price) : 0) : 1,
            errors: [],
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleChangeCleave = this.handleChangeCleave.bind(this);
        this.handleChangeSelect = this.handleChangeSelect.bind(this);

        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => {
        const { price } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.value;

        if(name === "quantity"){
            this.setState({ totalHt: helper.getTotalHt(value, price) });
        }

        this.setState({[name]: value})
    }

    handleChangeCleave = (e) => {
        const { quantity } = this.state;

        let name = e.currentTarget.name;
        let value = e.currentTarget.rawValue;

        if(name === "price"){
            this.setState({ totalHt: helper.getTotalHt(quantity, value) });
        }

        this.setState({ [e.currentTarget.name]: e.currentTarget.rawValue })
    }

    handleChangeSelect = (name, e) => {
        helper.setRateTva(this, this.props.taxes, name, e);
        this.setState({ [name]: e !== undefined ? e.value : "" })
    }
    handleSubmit = (e) => {
        e.preventDefault();

        const { name, reference } = this.state;

        this.setState({ errors: [] })

        let paramsToValidate = [
            {type: "text", id: 'name', value: name}
        ];

        if(reference !== ""){
            paramsToValidate = [...paramsToValidate,
                ...[
                    {type: "text", id: 'reference', value: reference},
                    {type: "length", id: 'reference', value: reference, min: 0, max: 10}
                ]
            ];
        }

        // validate global
        let validate = Validateur.validateur(paramsToValidate)
        if(!validate.code){
            Formulaire.showErrors(this, validate, "Veuillez vérifier les informations transmises.", false);
        }else{
            this.props.onSubmit(this.state);
            this.setState({ uid: uid(16) })
        }
    }

    render () {
        const { taxes, unities, selectItems, onSelectItem, onCloseAdd, step, item } = this.props;
        const { errors, reference, numero, name, content, unity, price, codeTva, quantity, totalHt } = this.state;

        let [selectTvas, selectUnities] = helper.getTaxesAndUnitiesSelectItems(taxes, unities);

        return <>
            <div className="item item-add">
                <div className="item-content">
                    <div className="item-body">
                        <div className="infos infos-col-7">
                            <div className="col-1">
                                <div className="bloc-edit-cancel" onClick={() => onCloseAdd(step)}>
                                    <ButtonIcon icon="cancel">Fermer</ButtonIcon>
                                </div>
                            </div>
                            <div className="col-2">
                                <div className="line line-select-special">
                                    <SelectReactSelectize items={selectItems} identifiant="item" placeholder={"Sélectionner"}
                                                          valeur={item} errors={errors} onChange={(e) => onSelectItem(e)}>
                                        <span className="icon-box" /><span>Pré-remplir l'article</span>
                                    </SelectReactSelectize>
                                </div>
                            </div>
                            <div className="col-3" />
                            <div className="col-4" />
                            <div className="col-5" />
                            <div className="col-6" />
                            <div className="col-7" />
                        </div>

                    </div>
                    <div className="item-body">
                        <div className="infos infos-col-7">
                            <div className="col-1">
                                <div className="line">
                                    <Input valeur={reference} identifiant="reference" errors={errors} onChange={this.handleChange} placeholder="Ref (max 10 caractères)" />
                                </div>
                                <div className="line">
                                    <Input valeur={numero} identifiant="numero" errors={errors} onChange={this.handleChange} placeholder="Numéro comptable" />
                                </div>
                            </div>
                            <div className="col-2">
                                <div className="line">
                                    <Input valeur={name} identifiant="name" errors={errors} onChange={this.handleChange} placeholder="* Désignation" />
                                </div>
                                <div className="line">
                                    <TextArea valeur={content} identifiant="content" errors={errors} onChange={this.handleChange} placeholder="Description" />
                                </div>
                            </div>
                            <div className="col-3">
                                <div className="line">
                                    <Input type="number" valeur={quantity} identifiant="quantity" errors={errors} onChange={this.handleChange} placeholder="Quantité" />
                                </div>
                            </div>
                            <div className="col-4">
                                <div className="line">
                                    <SelectReactSelectize items={selectUnities} identifiant="unity" placeholder="Unité"
                                                          valeur={unity} errors={errors} onChange={(e) => this.handleChangeSelect('unity', e)} />
                                </div>
                            </div>
                            <div className="col-5">
                                <div className="line">
                                    <Input type="cleave" valeur={price} identifiant="price" errors={errors} onChange={this.handleChangeCleave} placeholder="€" />
                                </div>
                                <div className="line">
                                    <div className="form-group">
                                        <label>Total HT</label>
                                        <div className="sub">{Sanitaze.toFormatCurrency(totalHt)}</div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="line">
                                    <SelectReactSelectize items={selectTvas} identifiant="codeTva" placeholder="Taux de TVA"
                                                          valeur={codeTva} errors={errors} onChange={(e) => this.handleChangeSelect('codeTva', e)} />
                                </div>
                            </div>
                            <div className="col-7 actions">
                                <ButtonIcon icon="add-square" isSubmit={false} onClick={this.handleSubmit}>Ajouter</ButtonIcon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}
