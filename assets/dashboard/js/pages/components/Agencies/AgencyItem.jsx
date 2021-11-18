import React, { Component } from 'react';

import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import Sanitize         from "@commonComponents/functions/sanitaze";

export class AgencyItem extends Component {
    render () {
        const { elem, total, onChangeContext, onDelete } = this.props

        let progress;
        let nb = (elem.totalBiens / total) * 100; // sub le total global des agences
        if(nb === 0){
            progress = 0;
        }else if(nb > 0 && nb < 26){
            progress = 25;
        }else if(nb >= 26 && nb < 51){
            progress = 50;
        }else if(nb >= 51 && nb < 76){
            progress = 75;
        }else{
            progress = 100;
        }

        return <div className="item">
            <div className="item-content">
                <div className="item-body item-body-image">
                    <div className="item-image">
                        <img src={`/immo/logos/${elem.logo}`} alt={`Image de ${elem.name}`}/>
                    </div>
                    <div className="infos infos-col-4">
                        <div className="col-1" onClick={() => onChangeContext("read", elem)}>
                            <div className="name">
                                <span>{elem.name}</span>
                                <a target="_blank" href={elem.website}><span className="icon-link-2" /></a>
                            </div>
                            <div className="sub">{elem.dirname}</div>
                            <div className="sub">{elem.email}</div>
                            <div className="sub">{Sanitize.toFormatPhone(elem.phone)}</div>
                        </div>
                        <div className="col-2">
                            <div className="sub">{elem.address},</div>
                            <div className="sub">{elem.zipcode} {elem.city}</div>
                        </div>
                        <div className="col-3">
                            <div className="sub">{elem.totalBiens}</div>
                            <div className={"sub progress progress-" + progress} />
                        </div>
                        <div className="col-4 actions">
                            <ButtonIcon icon="pencil" onClick={() => onChangeContext("update", elem)}>Modifier</ButtonIcon>
                            <ButtonIcon icon="trash" onClick={() => onDelete(elem)}>Supprimer</ButtonIcon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

