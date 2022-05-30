import React, {Component} from "react";

import Sanitaze from "@commonComponents/functions/sanitaze";

import { ButtonIcon } from "@dashboardComponents/Tools/Button";
import { ItemInvoiceFormulaire } from "@dashboardPages/components/Bill/components/ItemInvoiceForm";

let i = 0;

export class Products extends Component {
    render () {
        const { step, edit, onClickEdit, type, onChangeItems, onSelectItem, onUpdateItem, item, items,
            element, societyId, taxes, unities, products, noStep } = this.props;

        let selectItems = [];
        items.forEach(it => {
            selectItems.push({ value: it.id, label: it.name + " : " + Sanitaze.toFormatCurrency(it.price), identifiant: "it-" + it.id })
        })

        return <div className="items-table">
            <div className="items items-default">
                <div className="item item-header">
                    <div className="item-content">
                        <div className="item-body">
                            <div className="infos infos-col-7">
                                <div className="col-1">Référence</div>
                                <div className="col-2">Désignation</div>
                                <div className="col-3">Quantité</div>
                                <div className="col-4">Unité</div>
                                <div className="col-5">P.U HT</div>
                                <div className="col-6">Code TVA</div>
                                <div className="col-7">Actions</div>
                            </div>
                        </div>
                    </div>
                </div>
                {products.map((pr, index) => {
                    return <div className="item" key={index}>
                        <div className="item-content">
                            <div className="item-body">
                                <div className="infos infos-col-7">
                                    <div className="col-1">
                                        <div className="name">{pr.reference}</div>
                                        <div className="sub">{pr.numero}</div>
                                    </div>
                                    <div className="col-2">
                                        <div className="sub">{pr.name}</div>
                                        <div className="sub">{pr.content}</div>
                                    </div>
                                    <div className="col-3">
                                        <div className="sub"><span className="forMobile">Quantité : </span>{pr.quantity}</div>
                                    </div>
                                    <div className="col-4">
                                        <div className="sub"><span className="forMobile">Unité : </span>{pr.unity}</div>
                                    </div>
                                    <div className="col-5">
                                        <div className="sub">{Sanitaze.toFormatCurrency(pr.price)}</div>
                                    </div>
                                    <div className="col-6">
                                        {pr.codeTva !== "" && <div className="sub"><span className="forMobile">Code TVA : </span>{pr.codeTva}</div>}
                                    </div>
                                    <div className="col-7 actions">
                                        <ButtonIcon icon="pencil" onClick={() => onUpdateItem(pr)}>Modifier</ButtonIcon>
                                        <ButtonIcon icon="trash" onClick={() => onChangeItems(pr, "delete")}>Supprimer</ButtonIcon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                })}

                {edit === step ? <>
                    <ItemInvoiceFormulaire type={type} selectItems={selectItems} item={item} step={step} onSelectItem={onSelectItem} onCloseAdd={onClickEdit}
                                           onSubmit={onChangeItems} element={element} societyId={societyId} taxes={taxes} unities={unities} noStep={noStep} key={i++} />
                </> : <>
                    <div className="bloc-edit" onClick={() => onClickEdit(step)}>
                        <div className="bloc-edit-icon bloc-edit-add">
                            <ButtonIcon icon="add">Ajouter</ButtonIcon>
                        </div>
                        <div className="bloc-edit-content">
                            Ajouter un article
                        </div>
                    </div>
                </>}

            </div>
        </div>
    }
}