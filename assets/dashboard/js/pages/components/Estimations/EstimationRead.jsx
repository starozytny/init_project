import React, { Component } from 'react';

import { Button }        from "@dashboardComponents/Tools/Button";

export class EstimationRead extends Component {
    render () {
        const { elem, onChangeContext } = this.props;

        return <>
            <div>
                <div className="toolbar">
                    <div className="item">
                        <Button outline={true} icon="left-arrow" type="primary" onClick={() => onChangeContext("list")}>Retour à la liste</Button>
                    </div>
                </div>

                <div className="item-estimation-read">
                    <div className="time">{elem.createdAtString}, {elem.createdAtAgo}</div>

                    <div className="item-estimation-read-line">
                        <div className="contact">
                            <div className="name">{elem.lastname.toUpperCase()} {elem.firstname}</div>
                            <div className="sub">{elem.phone}</div>
                            {elem.email && <div className="sub">{elem.email}</div>}
                        </div>

                        <div className="infos">
                            <Item val={elem.zipcode + ", " + elem.city}>Adresse</Item>
                            <Item val={elem.typeAd}>Nature</Item>
                            <Item val={elem.typeBien}>Type de bien</Item>
                            <Item val={elem.etat}>Etat du bien</Item>
                        </div>
                    </div>

                    <div className="item-estimation-read-line">
                        <div className="feature">
                            <Item val={elem.constructionYear}>Année de construction</Item>
                            <Item val={elem.area + " m²"}>Surface</Item>
                            <Item val={elem.areaLand ? elem.arealand + " m²" : ""}>Surface du terrain</Item>
                            <Item val={elem.nbPiece}>Nombre de pièces</Item>
                            <Item val={elem.nbRoom}>Nombre de chambres</Item>
                            <Item val={elem.nbParking}>Nombre de parkings</Item>
                            <Item val={elem.extString}>Extérieurs</Item>
                        </div>
                        <div className="infos">
                            <div className="item">
                                <div className="label">Informations supplémentaires : </div>
                            </div>
                            <div className="sub-message">{elem.infos}</div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    }
}

function Item({ children, val })
{
    let content = "";
    if(val){
        content = <div className="item">
            <div className="label">{children} :</div>
            <div>{val}</div>
        </div>
    }

    return content;
}